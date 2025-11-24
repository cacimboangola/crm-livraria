<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LoyaltyService;
use App\Models\Customer;
use App\Models\LoyaltyPoint;
use App\Models\LoyaltyTransaction;
use Illuminate\Support\Facades\Auth;

class LoyaltyController extends Controller
{
    protected $loyaltyService;
    
    /**
     * Construtor do controlador.
     *
     * @param \App\Services\LoyaltyService $loyaltyService
     * @return void
     */
    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }
    
    /**
     * Exibe o painel de fidelidade para um cliente.
     *
     * @param \App\Models\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function customerDashboard(Customer $customer)
    {
        // Obter os pontos de fidelidade do cliente
        $loyaltyPoints = $this->loyaltyService->getOrCreateLoyaltyPoints($customer->id);
        
        // Obter o histórico de transações
        $transactions = $this->loyaltyService->getTransactionHistory($customer->id);
        
        return view('loyalty.dashboard', compact('customer', 'loyaltyPoints', 'transactions'));
    }
    
    /**
     * Exibe o histórico de transações de pontos para um cliente.
     *
     * @param \App\Models\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function transactionHistory(Customer $customer)
    {
        // Obter os pontos de fidelidade do cliente
        $loyaltyPoints = $this->loyaltyService->getOrCreateLoyaltyPoints($customer->id);
        
        // Obter o histórico de transações
        $transactions = $this->loyaltyService->getTransactionHistory($customer->id, 100);
        
        return view('loyalty.transactions', compact('customer', 'loyaltyPoints', 'transactions'));
    }
    
    /**
     * Exibe o formulário para adicionar pontos manualmente.
     *
     * @param \App\Models\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function showAddPointsForm(Customer $customer)
    {
        $loyaltyPoints = $this->loyaltyService->getOrCreateLoyaltyPoints($customer->id);
        
        return view('loyalty.add-points', compact('customer', 'loyaltyPoints'));
    }
    
    /**
     * Processa a adição manual de pontos.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function addPoints(Request $request, Customer $customer)
    {
        $request->validate([
            'points' => 'required|integer|min:1',
            'description' => 'required|string|max:255',
        ]);
        
        $points = $request->input('points');
        $description = $request->input('description');
        
        // Adicionar pontos
        $this->loyaltyService->adjustPoints(
            $customer->id,
            $points,
            $description,
            ['added_by' => Auth::id(), 'reason' => 'manual_adjustment']
        );
        
        return redirect()->route('loyalty.dashboard', $customer)
            ->with('success', "Foram adicionados {$points} pontos para o cliente.");
    }
    
    /**
     * Exibe o formulário para resgatar pontos.
     *
     * @param \App\Models\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function showRedeemPointsForm(Customer $customer)
    {
        $loyaltyPoints = $this->loyaltyService->getOrCreateLoyaltyPoints($customer->id);
        
        return view('loyalty.redeem-points', compact('customer', 'loyaltyPoints'));
    }
    
    /**
     * Processa o resgate de pontos.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Customer $customer
     * @return \Illuminate\Http\Response
     */
    public function redeemPoints(Request $request, Customer $customer)
    {
        $request->validate([
            'points' => 'required|integer|min:1',
            'description' => 'required|string|max:255',
        ]);
        
        $points = $request->input('points');
        $description = $request->input('description');
        
        // Obter os pontos de fidelidade do cliente
        $loyaltyPoints = $this->loyaltyService->getOrCreateLoyaltyPoints($customer->id);
        
        // Verificar se o cliente tem pontos suficientes
        if (!$loyaltyPoints->hasEnoughPoints($points)) {
            return redirect()->back()->withErrors(['points' => 'O cliente não possui pontos suficientes para este resgate.']);
        }
        
        // Resgatar pontos
        $transaction = $this->loyaltyService->redeemPoints(
            $customer->id,
            $points,
            $description,
            ['redeemed_by' => Auth::id()]
        );
        
        if (!$transaction) {
            return redirect()->back()->withErrors(['points' => 'Não foi possível processar o resgate de pontos.']);
        }
        
        return redirect()->route('loyalty.dashboard', $customer)
            ->with('success', "Foram resgatados {$points} pontos do cliente.");
    }
    
    /**
     * Exibe o painel administrativo do programa de fidelidade.
     *
     * @return \Illuminate\Http\Response
     */
    public function adminDashboard()
    {
        // Estatísticas gerais do programa de fidelidade
        $totalPoints = LoyaltyPoint::sum('current_balance');
        $totalCustomers = LoyaltyPoint::count();
        $totalTransactions = LoyaltyTransaction::count();
        
        // Distribuição de níveis
        $levelDistribution = LoyaltyPoint::selectRaw('level, count(*) as count')
            ->groupBy('level')
            ->get()
            ->pluck('count', 'level')
            ->toArray();
        
        // Transações recentes
        $recentTransactions = LoyaltyTransaction::with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('loyalty.admin', compact(
            'totalPoints', 
            'totalCustomers', 
            'totalTransactions', 
            'levelDistribution', 
            'recentTransactions'
        ));
    }
    
    /**
     * Executa a expiração de pontos antigos.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function processExpiration(Request $request)
    {
        $request->validate([
            'months' => 'required|integer|min:1|max:36',
        ]);
        
        $months = $request->input('months', 12);
        
        // Processar expiração
        $expiredCount = $this->loyaltyService->processPointsExpiration($months);
        
        return redirect()->route('loyalty.admin')
            ->with('success', "Processada a expiração de pontos para {$expiredCount} clientes.");
    }
}
