<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Campaign;
use App\Models\Customer;

class CampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * A campanha.
     *
     * @var \App\Models\Campaign
     */
    public $campaign;

    /**
     * O cliente.
     *
     * @var \App\Models\Customer
     */
    public $customer;

    /**
     * Create a new message instance.
     */
    public function __construct(Campaign $campaign, Customer $customer)
    {
        $this->campaign = $campaign;
        $this->customer = $customer;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->campaign->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.campaign',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Gera token de segurança para rastreamento
        $token = md5($this->campaign->id . $this->customer->id . $this->customer->email);
        
        // Adiciona pixel de rastreamento para abertura de email
        $trackingPixel = url("/track/open/{$this->campaign->id}/{$this->customer->id}/{$token}");
        
        // Processa o conteúdo da campanha para adicionar links de rastreamento
        $content = $this->processTrackingLinks($this->campaign->content, $token);
        
        return $this->view('emails.campaign')
                    ->with([
                        'campaign' => $this->campaign,
                        'customer' => $this->customer,
                        'content' => $content,
                        'trackingPixel' => $trackingPixel
                    ]);
    }
    
    /**
     * Processa links no conteúdo para adicionar rastreamento.
     *
     * @param string $content
     * @param string $token
     * @return string
     */
    protected function processTrackingLinks($content, $token)
    {
        // Encontra todos os links no conteúdo HTML
        $pattern = '/<a\s+(?:[^>]*?\s+)?href=(["\'])(.*?)\1/i';
        
        return preg_replace_callback($pattern, function($matches) use ($token) {
            $url = $matches[2];
            $trackingUrl = url("/track/click/{$this->campaign->id}/{$this->customer->id}/{$token}?url=" . urlencode($url));
            
            return '<a href="' . $trackingUrl . '"';
        }, $content);
    }
}
