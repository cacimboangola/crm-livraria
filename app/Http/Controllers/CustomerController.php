<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CustomerService;
use App\Models\Customer;

class CustomerController extends Controller
{
    protected $customerService;

    /**
     * Construtor do controlador.
     *
     * @param CustomerService $customerService
     */
    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
        $this->middleware('auth');
    }

    /**
     * Exibir uma lista de clientes.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = $this->customerService->getAllPaginated(10);
        return view('customers.index', compact('customers'));
    }

    /**
     * Mostrar o formulário para criar um novo cliente.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Armazenar um novo cliente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'tax_id' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $customer = $this->customerService->create($validated);

        return redirect()->route('customers.show', $customer->id)
            ->with('success', 'Cliente criado com sucesso!');
    }

    /**
     * Exibir um cliente específico.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    /**
     * Mostrar o formulário para editar um cliente.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Atualizar um cliente específico.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'tax_id' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $this->customerService->update($customer->id, $validated);

        return redirect()->route('customers.show', $customer->id)
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    /**
     * Remover um cliente específico.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $this->customerService->delete($customer->id);

        return redirect()->route('customers.index')
            ->with('success', 'Cliente removido com sucesso!');
    }

    /**
     * Buscar clientes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $term = $request->input('term');
        $customers = $this->customerService->search($term);

        return view('customers.index', compact('customers', 'term'));
    }
}
