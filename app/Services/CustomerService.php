<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerService
{
    /**
     * Obter todos os clientes com paginação
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return Customer::orderBy('name')->paginate($perPage);
    }

    /**
     * Obter todos os clientes
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return Customer::orderBy('name')->get();
    }

    /**
     * Obter cliente por ID
     *
     * @param int $id
     * @return Customer|null
     */
    public function getById(int $id): ?Customer
    {
        return Customer::find($id);
    }

    /**
     * Criar novo cliente e seu usuário correspondente
     *
     * @param array $data
     * @return Customer
     */
    public function create(array $data): Customer
    {
        // Criar o cliente
        $customer = Customer::create($data);
        
        // Criar um usuário associado ao cliente
        $tempPassword = Str::random(10);
        
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($tempPassword),
            'role' => 'customer',
        ]);
        
        // Aqui você pode enviar um e-mail com a senha temporária para o cliente
        // Exemplo: Mail::to($data['email'])->send(new CustomerWelcome($tempPassword));
        
        return $customer;
    }

    /**
     * Atualizar cliente existente
     *
     * @param int $id
     * @param array $data
     * @return Customer|null
     */
    public function update(int $id, array $data): ?Customer
    {
        $customer = $this->getById($id);
        
        if ($customer) {
            $customer->update($data);
            return $customer->fresh();
        }
        
        return null;
    }

    /**
     * Excluir cliente
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $customer = $this->getById($id);
        
        if ($customer) {
            return $customer->delete();
        }
        
        return false;
    }

    /**
     * Buscar clientes
     *
     * @param string $term
     * @return Collection
     */
    public function search(string $term): Collection
    {
        return Customer::where('name', 'like', "%{$term}%")
            ->orWhere('email', 'like', "%{$term}%")
            ->orWhere('phone', 'like', "%{$term}%")
            ->orWhere('tax_id', 'like', "%{$term}%")
            ->orderBy('name')
            ->get();
    }
}
