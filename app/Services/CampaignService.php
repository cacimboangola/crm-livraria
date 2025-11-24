<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Customer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use App\Mail\CampaignMail;
use Carbon\Carbon;
use App\Services\LoyaltyService;

class CampaignService
{
    protected $loyaltyService;
    
    /**
     * Construtor do serviço.
     *
     * @param LoyaltyService $loyaltyService
     */
    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }
    /**
     * Cria uma nova campanha.
     *
     * @param  array  $data
     * @return \App\Models\Campaign
     */
    public function createCampaign(array $data): Campaign
    {
        return Campaign::create($data);
    }

    /**
     * Atualiza uma campanha existente.
     *
     * @param  int  $id
     * @param  array  $data
     * @return \App\Models\Campaign|null
     */
    public function updateCampaign(int $id, array $data): ?Campaign
    {
        $campaign = Campaign::find($id);
        
        if ($campaign) {
            $campaign->update($data);
        }
        
        return $campaign;
    }

    /**
     * Obtém uma campanha pelo ID.
     *
     * @param  int  $id
     * @return \App\Models\Campaign|null
     */
    public function getCampaign(int $id): ?Campaign
    {
        return Campaign::with('customers')->find($id);
    }

    /**
     * Lista todas as campanhas com paginação.
     *
     * @param  int  $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function listCampaigns(int $perPage = 15)
    {
        return Campaign::orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Lista campanhas ativas.
     *
     * @param  int  $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function listActiveCampaigns(int $perPage = 15)
    {
        return Campaign::active()->orderBy('start_date', 'desc')->paginate($perPage);
    }

    /**
     * Exclui uma campanha.
     *
     * @param  int  $id
     * @return bool
     */
    public function deleteCampaign(int $id): bool
    {
        $campaign = Campaign::find($id);
        
        if ($campaign) {
            return $campaign->delete();
        }
        
        return false;
    }

    /**
     * Ativa uma campanha.
     *
     * @param  int  $id
     * @return \App\Models\Campaign|null
     */
    public function activateCampaign(int $id): ?Campaign
    {
        $campaign = Campaign::find($id);
        
        if ($campaign) {
            $campaign->status = 'active';
            $campaign->save();
        }
        
        return $campaign;
    }

    /**
     * Cancela uma campanha.
     *
     * @param  int  $id
     * @return \App\Models\Campaign|null
     */
    public function cancelCampaign(int $id): ?Campaign
    {
        $campaign = Campaign::find($id);
        
        if ($campaign) {
            $campaign->status = 'cancelled';
            $campaign->save();
        }
        
        return $campaign;
    }

    /**
     * Conclui uma campanha.
     *
     * @param  int  $id
     * @return \App\Models\Campaign|null
     */
    public function completeCampaign(int $id): ?Campaign
    {
        $campaign = Campaign::find($id);
        
        if ($campaign) {
            $campaign->status = 'completed';
            $campaign->save();
        }
        
        return $campaign;
    }

    /**
     * Adiciona clientes a uma campanha.
     *
     * @param  int  $campaignId
     * @param  array  $customerIds
     * @return \App\Models\Campaign|null
     */
    public function addCustomersToCampaign(int $campaignId, array $customerIds): ?Campaign
    {
        $campaign = Campaign::find($campaignId);
        
        if ($campaign) {
            $campaign->customers()->syncWithoutDetaching($customerIds);
        }
        
        return $campaign;
    }

    /**
     * Remove clientes de uma campanha.
     *
     * @param  int  $campaignId
     * @param  array  $customerIds
     * @return \App\Models\Campaign|null
     */
    public function removeCustomersFromCampaign(int $campaignId, array $customerIds): ?Campaign
    {
        $campaign = Campaign::find($campaignId);
        
        if ($campaign) {
            $campaign->customers()->detach($customerIds);
        }
        
        return $campaign;
    }

    /**
     * Seleciona clientes para uma campanha com base nos critérios definidos.
     *
     * @param  int  $campaignId
     * @return \App\Models\Campaign|null
     */
    public function selectCustomersForCampaign(int $campaignId): ?Campaign
    {
        $campaign = Campaign::find($campaignId);
        
        if (!$campaign || !$campaign->target_criteria) {
            return $campaign;
        }
        
        $criteria = $campaign->target_criteria;
        $query = Customer::query()->where('active', true);
        
        // Aplicar critérios de segmentação
        if (!empty($criteria['min_purchases'])) {
            $query->has('invoices', '>=', $criteria['min_purchases']);
        }
        
        if (!empty($criteria['min_total_spent'])) {
            $query->whereHas('invoices', function ($q) use ($criteria) {
                $q->selectRaw('sum(total) as total')
                  ->groupBy('customer_id')
                  ->having('total', '>=', $criteria['min_total_spent']);
            });
        }
        
        if (!empty($criteria['categories'])) {
            $query->whereHas('invoices.items.book', function ($q) use ($criteria) {
                $q->whereIn('category_id', $criteria['categories']);
            });
        }
        
        if (!empty($criteria['days_since_last_purchase'])) {
            $date = Carbon::now()->subDays($criteria['days_since_last_purchase']);
            $query->whereHas('invoices', function ($q) use ($date) {
                $q->where('date', '>=', $date);
            });
        }
        
        $customers = $query->get();
        $customerIds = $customers->pluck('id')->toArray();
        
        $this->addCustomersToCampaign($campaignId, $customerIds);
        
        return $campaign->fresh('customers');
    }

    /**
     * Envia uma campanha por email para os clientes selecionados.
     *
     * @param  int  $campaignId
     * @return int  Número de emails enviados
     */
    public function sendCampaignEmails(int $campaignId): int
    {
        $campaign = Campaign::with(['customers' => function ($query) {
            $query->wherePivot('sent', false);
        }])->find($campaignId);
        
        if (!$campaign || $campaign->type !== 'email') {
            return 0;
        }
        
        $count = 0;
        
        foreach ($campaign->customers as $customer) {
            try {
                Mail::to($customer->email)->send(new CampaignMail($campaign, $customer));
                
                // Atualizar status de envio
                $campaign->customers()->updateExistingPivot($customer->id, [
                    'sent' => true,
                    'sent_at' => now(),
                ]);
                
                $count++;
            } catch (\Exception $e) {
                // Log do erro
                \Log::error("Erro ao enviar email da campanha {$campaign->id} para o cliente {$customer->id}: " . $e->getMessage());
            }
        }
        
        // Atualizar métricas da campanha
        $metrics = $campaign->metrics ?? [];
        $metrics['emails_sent'] = ($metrics['emails_sent'] ?? 0) + $count;
        $metrics['last_sent_at'] = now()->toDateTimeString();
        
        $campaign->metrics = $metrics;
        $campaign->save();
        
        return $count;
    }

    /**
     * Registra a abertura de um email de campanha.
     *
     * @param  int  $campaignId
     * @param  int  $customerId
     * @return bool
     */
    public function trackEmailOpen(int $campaignId, int $customerId): bool
    {
        $campaign = Campaign::find($campaignId);
        
        if (!$campaign) {
            return false;
        }
        
        $campaign->customers()->updateExistingPivot($customerId, [
            'opened' => true,
            'opened_at' => now(),
        ]);
        
        // Atualizar métricas da campanha
        $metrics = $campaign->metrics ?? [];
        $metrics['emails_opened'] = ($metrics['emails_opened'] ?? 0) + 1;
        
        $campaign->metrics = $metrics;
        $campaign->save();
        
        return true;
    }

    /**
     * Registra um clique em um link de campanha.
     *
     * @param  int  $campaignId
     * @param  int  $customerId
     * @return bool
     */
    public function trackEmailClick(int $campaignId, int $customerId): bool
    {
        $campaign = Campaign::find($campaignId);
        
        if (!$campaign) {
            return false;
        }
        
        $campaign->customers()->updateExistingPivot($customerId, [
            'clicked' => true,
            'clicked_at' => now(),
        ]);
        
        // Atualizar métricas da campanha
        $metrics = $campaign->metrics ?? [];
        $metrics['emails_clicked'] = ($metrics['emails_clicked'] ?? 0) + 1;
        
        $campaign->metrics = $metrics;
        $campaign->save();
        
        return true;
    }

    /**
     * Registra uma conversão de campanha (compra realizada).
     *
     * @param  int  $campaignId
     * @param  int  $customerId
     * @return bool
     */
    public function trackConversion(int $campaignId, int $customerId): bool
    {
        $campaign = Campaign::find($campaignId);
        
        if (!$campaign) {
            return false;
        }
        
        $campaign->customers()->updateExistingPivot($customerId, [
            'converted' => true,
            'converted_at' => now(),
        ]);
        
        // Atualizar métricas da campanha
        $metrics = $campaign->metrics ?? [];
        $metrics['conversions'] = ($metrics['conversions'] ?? 0) + 1;
        
        $campaign->metrics = $metrics;
        $campaign->save();
        
        return true;
    }

    /**
     * Obtém métricas resumidas de uma campanha.
     *
     * @param  int  $campaignId
     * @return array
     */
    public function getCampaignMetrics(int $campaignId): array
    {
        $campaign = Campaign::with('customers')->find($campaignId);
        
        if (!$campaign) {
            return [];
        }
        
        $totalCustomers = $campaign->customers->count();
        $sent = $campaign->customers()->wherePivot('sent', true)->count();
        $opened = $campaign->customers()->wherePivot('opened', true)->count();
        $clicked = $campaign->customers()->wherePivot('clicked', true)->count();
        $converted = $campaign->customers()->wherePivot('converted', true)->count();
        
        return [
            'total_customers' => $totalCustomers,
            'sent' => $sent,
            'sent_rate' => $totalCustomers > 0 ? ($sent / $totalCustomers) * 100 : 0,
            'opened' => $opened,
            'open_rate' => $sent > 0 ? ($opened / $sent) * 100 : 0,
            'clicked' => $clicked,
            'click_rate' => $sent > 0 ? ($clicked / $sent) * 100 : 0,
            'converted' => $converted,
            'conversion_rate' => $sent > 0 ? ($converted / $sent) * 100 : 0,
        ];
    }
    
    /**
     * Distribui pontos de fidelidade para os clientes da campanha.
     *
     * @param  int  $campaignId
     * @param  int  $points
     * @param  string  $description
     * @return int  Número de clientes que receberam pontos
     */
    public function distributeLoyaltyPoints(int $campaignId, int $points, string $description = ''): int
    {
        $campaign = Campaign::with('customers')->find($campaignId);
        
        if (!$campaign || $points <= 0) {
            return 0;
        }
        
        $count = 0;
        $description = $description ?: "Campanha: {$campaign->name}";
        
        foreach ($campaign->customers as $customer) {
            try {
                // Adicionar pontos ao cliente
                $this->loyaltyService->addPoints(
                    $customer->id,
                    $points,
                    $description,
                    'campaign',
                    $campaign->id
                );
                
                // Atualizar status de distribuição de pontos
                $campaign->customers()->updateExistingPivot($customer->id, [
                    'loyalty_points_given' => true,
                    'loyalty_points_amount' => $points,
                    'loyalty_points_given_at' => now(),
                ]);
                
                $count++;
            } catch (\Exception $e) {
                // Log do erro
                \Log::error("Erro ao distribuir pontos de fidelidade da campanha {$campaign->id} para o cliente {$customer->id}: " . $e->getMessage());
            }
        }
        
        // Atualizar métricas da campanha
        $metrics = $campaign->metrics ?? [];
        $metrics['loyalty_points_distributed'] = ($metrics['loyalty_points_distributed'] ?? 0) + ($points * $count);
        $metrics['loyalty_points_recipients'] = ($metrics['loyalty_points_recipients'] ?? 0) + $count;
        
        $campaign->metrics = $metrics;
        $campaign->save();
        
        return $count;
    }
}
