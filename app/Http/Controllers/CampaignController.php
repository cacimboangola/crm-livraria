<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CampaignService;
use App\Services\CustomerService;
use App\Services\LoyaltyService;
use App\Models\Campaign;

class CampaignController extends Controller
{
    protected $campaignService;
    protected $customerService;
    protected $loyaltyService;

    /**
     * Construtor.
     *
     * @param  \App\Services\CampaignService  $campaignService
     * @param  \App\Services\CustomerService  $customerService
     * @param  \App\Services\LoyaltyService  $loyaltyService
     * @return void
     */
    public function __construct(
        CampaignService $campaignService, 
        CustomerService $customerService,
        LoyaltyService $loyaltyService
    ) {
        $this->campaignService = $campaignService;
        $this->customerService = $customerService;
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Exibe uma lista de campanhas.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $campaigns = $this->campaignService->listCampaigns();
        return view('campaigns.index', compact('campaigns'));
    }

    /**
     * Exibe o formulário para criar uma nova campanha.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('campaigns.create');
    }

    /**
     * Armazena uma nova campanha.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'target_criteria' => 'nullable|array',
        ]);

        $campaign = $this->campaignService->createCampaign($validated);

        return redirect()->route('campaigns.show', $campaign->id)
            ->with('success', 'Campanha criada com sucesso.');
    }

    /**
     * Exibe uma campanha específica.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $campaign = $this->campaignService->getCampaign($id);
        
        if (!$campaign) {
            return redirect()->route('campaigns.index')
                ->with('error', 'Campanha não encontrada.');
        }
        
        $metrics = $this->campaignService->getCampaignMetrics($id);
        
        return view('campaigns.show', compact('campaign', 'metrics'));
    }

    /**
     * Exibe o formulário para editar uma campanha.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $campaign = $this->campaignService->getCampaign($id);
        
        if (!$campaign) {
            return redirect()->route('campaigns.index')
                ->with('error', 'Campanha não encontrada.');
        }
        
        return view('campaigns.edit', compact('campaign'));
    }

    /**
     * Atualiza uma campanha específica.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'target_criteria' => 'nullable|array',
        ]);

        $campaign = $this->campaignService->updateCampaign($id, $validated);
        
        if (!$campaign) {
            return redirect()->route('campaigns.index')
                ->with('error', 'Campanha não encontrada.');
        }

        return redirect()->route('campaigns.show', $campaign->id)
            ->with('success', 'Campanha atualizada com sucesso.');
    }

    /**
     * Remove uma campanha específica.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->campaignService->deleteCampaign($id);
        
        if ($result) {
            return redirect()->route('campaigns.index')
                ->with('success', 'Campanha excluída com sucesso.');
        } else {
            return redirect()->route('campaigns.index')
                ->with('error', 'Não foi possível excluir a campanha.');
        }
    }

    /**
     * Exibe a página para selecionar clientes para a campanha.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function selectCustomers($id)
    {
        $campaign = $this->campaignService->getCampaign($id);
        
        if (!$campaign) {
            return redirect()->route('campaigns.index')
                ->with('error', 'Campanha não encontrada.');
        }
        
        $customers = $this->customerService->getAllCustomers();
        
        return view('campaigns.select-customers', compact('campaign', 'customers'));
    }

    /**
     * Adiciona clientes selecionados à campanha.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addCustomers(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:customers,id',
        ]);

        $campaign = $this->campaignService->addCustomersToCampaign($id, $validated['customer_ids']);
        
        if (!$campaign) {
            return redirect()->route('campaigns.index')
                ->with('error', 'Campanha não encontrada.');
        }

        return redirect()->route('campaigns.show', $campaign->id)
            ->with('success', count($validated['customer_ids']) . ' clientes adicionados à campanha.');
    }

    /**
     * Remove clientes da campanha.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeCustomers(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:customers,id',
        ]);

        $campaign = $this->campaignService->removeCustomersFromCampaign($id, $validated['customer_ids']);
        
        if (!$campaign) {
            return redirect()->route('campaigns.index')
                ->with('error', 'Campanha não encontrada.');
        }

        return redirect()->route('campaigns.show', $campaign->id)
            ->with('success', count($validated['customer_ids']) . ' clientes removidos da campanha.');
    }

    /**
     * Seleciona clientes automaticamente com base nos critérios da campanha.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function autoSelectCustomers($id)
    {
        $campaign = $this->campaignService->selectCustomersForCampaign($id);
        
        if (!$campaign) {
            return redirect()->route('campaigns.index')
                ->with('error', 'Campanha não encontrada.');
        }

        return redirect()->route('campaigns.show', $campaign->id)
            ->with('success', 'Clientes selecionados automaticamente com base nos critérios da campanha.');
    }

    /**
     * Ativa uma campanha.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function activate($id)
    {
        $campaign = $this->campaignService->activateCampaign($id);
        
        if (!$campaign) {
            return redirect()->route('campaigns.index')
                ->with('error', 'Campanha não encontrada.');
        }

        return redirect()->route('campaigns.show', $campaign->id)
            ->with('success', 'Campanha ativada com sucesso.');
    }

    /**
     * Cancela uma campanha.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        $campaign = $this->campaignService->cancelCampaign($id);
        
        if (!$campaign) {
            return redirect()->route('campaigns.index')
                ->with('error', 'Campanha não encontrada.');
        }

        return redirect()->route('campaigns.show', $campaign->id)
            ->with('success', 'Campanha cancelada com sucesso.');
    }

    /**
     * Conclui uma campanha.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function complete($id)
    {
        $campaign = $this->campaignService->completeCampaign($id);
        
        if (!$campaign) {
            return redirect()->route('campaigns.index')
                ->with('error', 'Campanha não encontrada.');
        }

        return redirect()->route('campaigns.show', $campaign->id)
            ->with('success', 'Campanha concluída com sucesso.');
    }

    /**
     * Envia emails da campanha para os clientes selecionados.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sendEmails($id)
    {
        $count = $this->campaignService->sendCampaignEmails($id);
        
        return redirect()->route('campaigns.show', $id)
            ->with('success', $count . ' emails enviados com sucesso.');
    }
    
    /**
     * Distribui pontos de fidelidade para os clientes da campanha.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function distributePoints(Request $request, $id)
    {
        $validated = $request->validate([
            'points' => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
        ]);
        
        $campaign = $this->campaignService->getCampaign($id);
        
        if (!$campaign) {
            return redirect()->route('campaigns.index')
                ->with('error', 'Campanha não encontrada.');
        }
        
        // Usar o novo método centralizado no CampaignService
        $customersCount = $this->campaignService->distributeLoyaltyPoints(
            $id,
            $validated['points'],
            $validated['description'] ?? null
        );
        
        return redirect()->route('campaigns.show', $campaign->id)
            ->with('success', 'Pontos de fidelidade distribuídos para ' . $customersCount . ' clientes.');
    }

    /**
     * Rastreia a abertura de um email de campanha.
     *
     * @param  int  $campaignId
     * @param  int  $customerId
     * @return \Illuminate\Http\Response
     */
    public function trackOpen($campaignId, $customerId)
    {
        $this->campaignService->trackEmailOpen($campaignId, $customerId);
        
        // Retorna uma imagem transparente de 1x1 pixel
        return response()->file(public_path('images/pixel.gif'));
    }

    /**
     * Rastreia um clique em um link de campanha.
     *
     * @param  int  $campaignId
     * @param  int  $customerId
     * @param  string  $url
     * @return \Illuminate\Http\Response
     */
    public function trackClick($campaignId, $customerId, $url)
    {
        $this->campaignService->trackEmailClick($campaignId, $customerId);
        
        // Redireciona para o URL de destino
        return redirect($url);
    }

    /**
     * Exibe métricas detalhadas da campanha.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function metrics($id)
    {
        $campaign = $this->campaignService->getCampaign($id);
        
        if (!$campaign) {
            return redirect()->route('campaigns.index')
                ->with('error', 'Campanha não encontrada.');
        }
        
        $metrics = $this->campaignService->getCampaignMetrics($id);
        
        return view('campaigns.metrics', compact('campaign', 'metrics'));
    }
}
