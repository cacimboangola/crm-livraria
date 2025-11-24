<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Customer;
use App\Services\CampaignService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CampaignTrackingController extends Controller
{
    protected $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    /**
     * Rastreia a abertura de um email de campanha
     *
     * @param string $campaignId
     * @param string $customerId
     * @param string $token
     * @return \Illuminate\Http\Response
     */
    public function trackOpen($campaignId, $customerId, $token)
    {
        try {
            $campaign = Campaign::findOrFail($campaignId);
            $customer = Customer::findOrFail($customerId);
            
            // Verificar token de segurança
            $expectedToken = md5($campaign->id . $customer->id . $customer->email);
            
            if ($token !== $expectedToken) {
                Log::warning('Tentativa de rastreamento com token inválido', [
                    'campaign_id' => $campaignId,
                    'customer_id' => $customerId,
                    'token' => $token
                ]);
                
                // Retornar imagem de pixel transparente mesmo com token inválido
                return $this->generatePixel();
            }
            
            // Registrar abertura do email
            $this->campaignService->trackEmailOpen($campaign, $customer);
            
            // Retornar imagem de pixel transparente 1x1
            return $this->generatePixel();
        } catch (\Exception $e) {
            Log::error('Erro ao rastrear abertura de email: ' . $e->getMessage(), [
                'campaign_id' => $campaignId,
                'customer_id' => $customerId
            ]);
            
            // Retornar imagem de pixel transparente mesmo em caso de erro
            return $this->generatePixel();
        }
    }

    /**
     * Rastreia o clique em um link de campanha e redireciona para o URL original
     *
     * @param string $campaignId
     * @param string $customerId
     * @param string $token
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function trackClick($campaignId, $customerId, $token, Request $request)
    {
        $url = $request->query('url', '/');
        
        try {
            $campaign = Campaign::findOrFail($campaignId);
            $customer = Customer::findOrFail($customerId);
            
            // Verificar token de segurança
            $expectedToken = md5($campaign->id . $customer->id . $customer->email);
            
            if ($token !== $expectedToken) {
                Log::warning('Tentativa de rastreamento de clique com token inválido', [
                    'campaign_id' => $campaignId,
                    'customer_id' => $customerId,
                    'token' => $token,
                    'url' => $url
                ]);
                
                // Redirecionar mesmo com token inválido
                return redirect()->away($url);
            }
            
            // Registrar clique no link
            $this->campaignService->trackEmailClick($campaign, $customer);
            
            // Redirecionar para o URL original
            return redirect()->away($url);
        } catch (\Exception $e) {
            Log::error('Erro ao rastrear clique em link: ' . $e->getMessage(), [
                'campaign_id' => $campaignId,
                'customer_id' => $customerId,
                'url' => $url
            ]);
            
            // Redirecionar mesmo em caso de erro
            return redirect()->away($url);
        }
    }

    /**
     * Gera uma imagem de pixel transparente 1x1
     *
     * @return \Illuminate\Http\Response
     */
    protected function generatePixel()
    {
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        return response($pixel, 200)
            ->header('Content-Type', 'image/gif')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    /**
     * Registra uma conversão de campanha
     *
     * @param string $campaignId
     * @param string $customerId
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function trackConversion($campaignId, $customerId, $token)
    {
        try {
            $campaign = Campaign::findOrFail($campaignId);
            $customer = Customer::findOrFail($customerId);
            
            // Verificar token de segurança
            $expectedToken = md5($campaign->id . $customer->id . $customer->email);
            
            if ($token !== $expectedToken) {
                Log::warning('Tentativa de rastreamento de conversão com token inválido', [
                    'campaign_id' => $campaignId,
                    'customer_id' => $customerId,
                    'token' => $token
                ]);
                
                return response()->json(['success' => false, 'message' => 'Token inválido'], 403);
            }
            
            // Registrar conversão
            $this->campaignService->trackConversion($campaign, $customer);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Erro ao rastrear conversão: ' . $e->getMessage(), [
                'campaign_id' => $campaignId,
                'customer_id' => $customerId
            ]);
            
            return response()->json(['success' => false, 'message' => 'Erro ao processar conversão'], 500);
        }
    }
}
