<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Campaign;
use App\Models\LoyaltyPoint;
use App\Models\LoyaltyTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class LoyaltyService
{
    /**
     * Obtém ou cria um registro de pontos de fidelidade para um cliente.
     *
     * @param int $customerId
     * @return \App\Models\LoyaltyPoint
     */
    public function getOrCreateLoyaltyPoints(int $customerId): LoyaltyPoint
    {
        return LoyaltyPoint::firstOrCreate(
            ['customer_id' => $customerId],
            [
                'points' => 0,
                'points_spent' => 0,
                'points_expired' => 0,
                'current_balance' => 0,
                'level' => 'bronze',
                'level_expires_at' => now()->addYear(),
            ]
        );
    }
    
    /**
     * Obtém os pontos de fidelidade de um cliente.
     *
     * @param int $customerId
     * @return \App\Models\LoyaltyPoint
     */
    public function getCustomerPoints(int $customerId): LoyaltyPoint
    {
        return $this->getOrCreateLoyaltyPoints($customerId);
    }
    
    /**
     * Obtém o histórico de transações de pontos de fidelidade de um cliente.
     *
     * @param int $customerId
     * @param int $limit Número máximo de transações a retornar (opcional)
     * @return \Illuminate\Support\Collection
     */
    public function getCustomerTransactions(int $customerId, int $limit = 20): Collection
    {
        return LoyaltyTransaction::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Adiciona pontos para um cliente e registra a transação.
     *
     * @param int $customerId
     * @param int $points
     * @param string $description
     * @param Invoice|null $invoice
     * @param Campaign|null $campaign
     * @param array $metadata
     * @return \App\Models\LoyaltyTransaction
     */
    public function addPoints(
        int $customerId, 
        int $points, 
        string $description, 
        ?Invoice $invoice = null, 
        ?Campaign $campaign = null, 
        array $metadata = []
    ): LoyaltyTransaction {
        return DB::transaction(function () use ($customerId, $points, $description, $invoice, $campaign, $metadata) {
            $loyaltyPoints = $this->getOrCreateLoyaltyPoints($customerId);
            
            // Atualiza o saldo de pontos
            $loyaltyPoints->points += $points;
            $loyaltyPoints->current_balance += $points;
            $loyaltyPoints->save();
            
            // Atualiza o nível de fidelidade
            $loyaltyPoints->updateLevel();
            
            // Registra a transação
            $type = $campaign ? LoyaltyTransaction::TYPE_CAMPAIGN : LoyaltyTransaction::TYPE_EARN;
            if ($invoice) {
                $type = LoyaltyTransaction::TYPE_EARN;
            }
            
            $transaction = LoyaltyTransaction::create([
                'customer_id' => $customerId,
                'invoice_id' => $invoice ? $invoice->id : null,
                'campaign_id' => $campaign ? $campaign->id : null,
                'type' => $type,
                'points' => $points,
                'balance_after' => $loyaltyPoints->current_balance,
                'description' => $description,
                'metadata' => $metadata,
            ]);
            
            return $transaction;
        });
    }
    
    /**
     * Resgata pontos de um cliente e registra a transação.
     *
     * @param int $customerId
     * @param int $points
     * @param string $description
     * @param array $metadata
     * @return \App\Models\LoyaltyTransaction|null
     */
    public function redeemPoints(
        int $customerId, 
        int $points, 
        string $description, 
        array $metadata = []
    ): ?LoyaltyTransaction {
        return DB::transaction(function () use ($customerId, $points, $description, $metadata) {
            $loyaltyPoints = $this->getOrCreateLoyaltyPoints($customerId);
            
            // Verifica se o cliente tem pontos suficientes
            if (!$loyaltyPoints->hasEnoughPoints($points)) {
                return null;
            }
            
            // Atualiza o saldo de pontos
            $loyaltyPoints->points_spent += $points;
            $loyaltyPoints->current_balance -= $points;
            $loyaltyPoints->save();
            
            // Registra a transação
            $transaction = LoyaltyTransaction::create([
                'customer_id' => $customerId,
                'type' => LoyaltyTransaction::TYPE_REDEEM,
                'points' => -$points, // Valor negativo para indicar redução
                'balance_after' => $loyaltyPoints->current_balance,
                'description' => $description,
                'metadata' => $metadata,
            ]);
            
            return $transaction;
        });
    }
    
    /**
     * Adiciona pontos bônus para um cliente.
     *
     * @param int $customerId
     * @param int $points
     * @param string $description
     * @param Campaign|null $campaign
     * @param array $metadata
     * @return \App\Models\LoyaltyTransaction
     */
    public function addBonusPoints(
        int $customerId, 
        int $points, 
        string $description, 
        ?Campaign $campaign = null, 
        array $metadata = []
    ): LoyaltyTransaction {
        return DB::transaction(function () use ($customerId, $points, $description, $campaign, $metadata) {
            $loyaltyPoints = $this->getOrCreateLoyaltyPoints($customerId);
            
            // Atualiza o saldo de pontos
            $loyaltyPoints->points += $points;
            $loyaltyPoints->current_balance += $points;
            $loyaltyPoints->save();
            
            // Atualiza o nível de fidelidade
            $loyaltyPoints->updateLevel();
            
            // Registra a transação
            $transaction = LoyaltyTransaction::create([
                'customer_id' => $customerId,
                'campaign_id' => $campaign ? $campaign->id : null,
                'type' => LoyaltyTransaction::TYPE_BONUS,
                'points' => $points,
                'balance_after' => $loyaltyPoints->current_balance,
                'description' => $description,
                'metadata' => $metadata,
            ]);
            
            return $transaction;
        });
    }
    
    /**
     * Adiciona pontos para uma campanha específica.
     *
     * @param int $customerId
     * @param int $campaignId
     * @param int $points
     * @param string $description
     * @param array $metadata
     * @return \App\Models\LoyaltyTransaction
     */
    public function addCampaignPoints(
        int $customerId, 
        int $campaignId, 
        int $points, 
        string $description, 
        array $metadata = []
    ): LoyaltyTransaction {
        $campaign = Campaign::findOrFail($campaignId);
        
        return $this->addPoints(
            $customerId,
            $points,
            $description,
            null,
            $campaign,
            $metadata
        );
    }
    
    /**
     * Ajusta o saldo de pontos de um cliente.
     *
     * @param int $customerId
     * @param int $points
     * @param string $description
     * @param array $metadata
     * @return \App\Models\LoyaltyTransaction
     */
    public function adjustPoints(
        int $customerId, 
        int $points, 
        string $description, 
        array $metadata = []
    ): LoyaltyTransaction {
        return DB::transaction(function () use ($customerId, $points, $description, $metadata) {
            $loyaltyPoints = $this->getOrCreateLoyaltyPoints($customerId);
            
            // Atualiza o saldo de pontos
            if ($points > 0) {
                $loyaltyPoints->points += $points;
            } else {
                $loyaltyPoints->points_spent += abs($points);
            }
            
            $loyaltyPoints->current_balance += $points;
            $loyaltyPoints->save();
            
            // Atualiza o nível de fidelidade
            $loyaltyPoints->updateLevel();
            
            // Registra a transação
            $transaction = LoyaltyTransaction::create([
                'customer_id' => $customerId,
                'type' => LoyaltyTransaction::TYPE_ADJUST,
                'points' => $points,
                'balance_after' => $loyaltyPoints->current_balance,
                'description' => $description,
                'metadata' => $metadata,
            ]);
            
            return $transaction;
        });
    }
    
    /**
     * Processa a expiração de pontos para todos os clientes.
     *
     * @param int $olderThanMonths
     * @return int
     */
    public function processPointsExpiration(int $olderThanMonths = 12): int
    {
        $expirationDate = now()->subMonths($olderThanMonths);
        $expiredCount = 0;
        
        // Busca transações mais antigas que o período de expiração
        $customersWithExpirablePoints = LoyaltyTransaction::where('created_at', '<', $expirationDate)
            ->where('type', LoyaltyTransaction::TYPE_EARN)
            ->where('points', '>', 0)
            ->select('customer_id')
            ->distinct()
            ->get();
        
        foreach ($customersWithExpirablePoints as $customerData) {
            $customerId = $customerData->customer_id;
            $loyaltyPoints = $this->getOrCreateLoyaltyPoints($customerId);
            
            // Calcula pontos a expirar para este cliente
            $pointsToExpire = LoyaltyTransaction::where('customer_id', $customerId)
                ->where('created_at', '<', $expirationDate)
                ->where('type', LoyaltyTransaction::TYPE_EARN)
                ->where('points', '>', 0)
                ->sum('points');
            
            // Limita a expiração ao saldo atual
            $pointsToExpire = min($pointsToExpire, $loyaltyPoints->current_balance);
            
            if ($pointsToExpire > 0) {
                DB::transaction(function () use ($customerId, $pointsToExpire, $loyaltyPoints) {
                    // Atualiza o saldo de pontos
                    $loyaltyPoints->points_expired += $pointsToExpire;
                    $loyaltyPoints->current_balance -= $pointsToExpire;
                    $loyaltyPoints->save();
                    
                    // Registra a transação
                    LoyaltyTransaction::create([
                        'customer_id' => $customerId,
                        'type' => LoyaltyTransaction::TYPE_EXPIRE,
                        'points' => -$pointsToExpire, // Valor negativo para indicar redução
                        'balance_after' => $loyaltyPoints->current_balance,
                        'description' => 'Expiração automática de pontos não utilizados',
                        'metadata' => [
                            'expiration_date' => $expirationDate->toDateString(),
                            'expiration_period_months' => $olderThanMonths
                        ],
                    ]);
                });
                
                $expiredCount++;
            }
        }
        
        return $expiredCount;
    }
    
    /**
     * Obtém o histórico de transações de um cliente.
     *
     * @param int $customerId
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getTransactionHistory(int $customerId, int $limit = 50): Collection
    {
        return LoyaltyTransaction::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Calcula os pontos a serem concedidos com base no valor da compra.
     *
     * @param float $purchaseAmount
     * @param string $customerLevel
     * @return int
     */
    public function calculatePointsForPurchase(float $purchaseAmount, string $customerLevel = 'bronze'): int
    {
        // Pontos base: 1 ponto para cada R$ 1,00 gasto
        $basePoints = floor($purchaseAmount);
        
        // Multiplicador com base no nível do cliente
        $multiplier = match($customerLevel) {
            'platinum' => 2.5,
            'gold' => 2.0,
            'silver' => 1.5,
            default => 1.0, // bronze
        };
        
        return (int) floor($basePoints * $multiplier);
    }
    
    /**
     * Adiciona pontos para uma compra (fatura).
     *
     * @param Invoice $invoice
     * @return \App\Models\LoyaltyTransaction|null
     */
    public function addPointsForPurchase(Invoice $invoice): ?LoyaltyTransaction
    {
        // Verifica se a fatura está paga
        if ($invoice->status !== 'paid') {
            return null;
        }
        
        $customer = $invoice->customer;
        $loyaltyPoints = $this->getOrCreateLoyaltyPoints($customer->id);
        
        // Calcula os pontos com base no valor da fatura e nível do cliente
        $points = $this->calculatePointsForPurchase(
            $invoice->total,
            $loyaltyPoints->level
        );
        
        // Adiciona os pontos
        return $this->addPoints(
            $customer->id,
            $points,
            "Pontos por compra - Fatura #{$invoice->id}",
            $invoice,
            null,
            [
                'invoice_number' => $invoice->number,
                'invoice_amount' => $invoice->total,
                'points_multiplier' => match($loyaltyPoints->level) {
                    'platinum' => 2.5,
                    'gold' => 2.0,
                    'silver' => 1.5,
                    default => 1.0, // bronze
                },
            ]
        );
    }
    
    /**
     * Adiciona pontos de fidelidade baseado em uma fatura.
     * Alias para addPointsForPurchase para compatibilidade.
     *
     * @param Invoice $invoice
     * @return \App\Models\LoyaltyTransaction|null
     */
    public function addPointsFromInvoice(Invoice $invoice): ?LoyaltyTransaction
    {
        return $this->addPointsForPurchase($invoice);
    }
}
