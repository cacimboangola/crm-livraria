<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\InvoiceService;
use App\Services\CustomerService;
use App\Services\BookService;
use App\Services\LoyaltyService;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;

class InvoiceController extends Controller
{
    protected $invoiceService;
    protected $customerService;
    protected $bookService;
    protected $loyaltyService;

    /**
     * Construtor do controlador.
     *
     * @param InvoiceService $invoiceService
     * @param CustomerService $customerService
     * @param BookService $bookService
     * @param LoyaltyService $loyaltyService
     */
    public function __construct(
        InvoiceService $invoiceService,
        CustomerService $customerService,
        BookService $bookService,
        LoyaltyService $loyaltyService
    ) {
        $this->invoiceService = $invoiceService;
        $this->customerService = $customerService;
        $this->bookService = $bookService;
        $this->loyaltyService = $loyaltyService;
        $this->middleware('auth');
    }

    /**
     * Exibir uma lista de faturas.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];

        $invoices = $this->invoiceService->getAllPaginated(10, $filters);
        return view('invoices.index', compact('invoices', 'filters'));
    }

    /**
     * Mostrar o formulário para criar uma nova fatura.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customers = $this->customerService->getAll();
        $books = $this->bookService->getAll(true);
        return view('invoices.create', compact('customers', 'books'));
    }

    /**
     * Armazenar uma nova fatura.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'status' => 'required|in:draft,pending,paid,cancelled',
            'notes' => 'nullable|string',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'tax_percentage' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.book_id' => 'required|exists:books,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        // Adicionar o usuário atual como criador da fatura
        $validated['user_id'] = Auth::id();

        // Separar itens dos dados da fatura
        $items = $validated['items'];
        unset($validated['items']);

        $invoice = $this->invoiceService->create($validated, $items);

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Fatura criada com sucesso!');
    }

    /**
     * Exibir uma fatura específica.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Mostrar o formulário para editar uma fatura.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        // Não permitir edição de faturas pagas ou canceladas
        if ($invoice->status === 'paid' || $invoice->status === 'cancelled') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Não é possível editar uma fatura paga ou cancelada.');
        }

        $customers = $this->customerService->getAll();
        $books = $this->bookService->getAll(true);
        return view('invoices.edit', compact('invoice', 'customers', 'books'));
    }

    /**
     * Atualizar uma fatura específica.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        // Não permitir edição de faturas pagas ou canceladas
        if ($invoice->status === 'paid' || $invoice->status === 'cancelled') {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Não é possível editar uma fatura paga ou cancelada.');
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'status' => 'required|in:draft,pending,paid,cancelled',
            'notes' => 'nullable|string',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'tax_percentage' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:invoice_items,id',
            'items.*.book_id' => 'required|exists:books,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $this->invoiceService->update($invoice->id, $validated);

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Fatura atualizada com sucesso!');
    }

    /**
     * Remover uma fatura específica.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        // Não permitir exclusão de faturas pagas
        if ($invoice->status === 'paid') {
            return redirect()->route('invoices.index')
                ->with('error', 'Não é possível excluir uma fatura paga.');
        }

        $this->invoiceService->delete($invoice->id);

        return redirect()->route('invoices.index')
            ->with('success', 'Fatura removida com sucesso!');
    }

    /**
     * Buscar faturas.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $term = $request->input('term');
        $invoices = $this->invoiceService->search($term);

        return view('invoices.index', compact('invoices', 'term'));
    }

    /**
     * Alterar o status de uma fatura.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,pending,paid,cancelled',
        ]);
        
        $newStatus = $validated['status'];
        
        // O InvoiceService agora gerencia automaticamente os pontos de fidelidade
        $this->invoiceService->changeStatus($invoice->id, $newStatus);

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Status da fatura alterado com sucesso!');
    }

    /**
     * Gerar PDF da fatura.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function generatePdf(Invoice $invoice)
    {
        // Recarregar a fatura com os relacionamentos necessários para a visualização
        $invoice = Invoice::with(['items.book', 'customer'])->findOrFail($invoice->id);
        
        $pdf = PDF::loadView('invoices.print', compact('invoice'));
        
        // Configurar o PDF
        $pdf->setPaper('a4');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif'
        ]);
        
        // Nome do arquivo
        $fileName = 'fatura_' . $invoice->id . '_' . date('Y-m-d') . '.pdf';
        
        // Retornar o PDF para download
        return $pdf->download($fileName);
    }

    /**
     * Enviar fatura por e-mail.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function sendEmail(Request $request, Invoice $invoice)
    {
        // Validar o email de destino
        $request->validate([
            'email' => 'required|email',
        ]);
        
        $email = $request->input('email', $invoice->customer->email);
        
        try {
            // Enviar o email com a fatura
            Mail::to($email)->send(new InvoiceMail($invoice));
            
            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', 'Fatura enviada por email com sucesso para ' . $email);
        } catch (\Exception $e) {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('error', 'Erro ao enviar email: ' . $e->getMessage());
        }
    }
    

}
