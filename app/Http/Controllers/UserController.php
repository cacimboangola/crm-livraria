<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Construtor do controlador.
     */
    public function __construct()
    {
        $this->middleware('auth');
        // Apenas administradores podem gerenciar usuários
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isAdmin()) {
                return redirect()->route('dashboard')
                    ->with('error', 'Você não tem permissão para acessar esta área.');
            }
            return $next($request);
        })->except(['profile', 'updateProfile', 'changePassword']);
    }

    /**
     * Exibir uma lista de usuários.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Mostrar o formulário para criar um novo usuário.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Armazenar um novo usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'role' => ['required', Rule::in(['admin', 'manager', 'cashier', 'employee'])],
            'active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return redirect()->route('users.show', $user->id)
            ->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Exibir um usuário específico.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Mostrar o formulário para editar um usuário.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Atualizar um usuário específico.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => ['required', Rule::in(['admin', 'manager', 'cashier', 'employee'])],
            'active' => 'boolean',
        ]);

        // Não permitir que o último administrador seja desativado ou tenha seu papel alterado
        if ($user->isAdmin() && $user->active && 
            (($validated['role'] !== 'admin') || !$validated['active'])) {
            
            $adminCount = User::where('role', 'admin')->where('active', true)->count();
            
            if ($adminCount <= 1) {
                return redirect()->route('users.edit', $user->id)
                    ->with('error', 'Não é possível alterar o último administrador ativo.')
                    ->withInput();
            }
        }

        $user->update($validated);

        return redirect()->route('users.show', $user->id)
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remover um usuário específico.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // Não permitir exclusão do próprio usuário
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'Você não pode excluir seu próprio usuário.');
        }

        // Não permitir exclusão do último administrador
        if ($user->isAdmin()) {
            $adminCount = User::where('role', 'admin')->where('active', true)->count();
            if ($adminCount <= 1) {
                return redirect()->route('users.index')
                    ->with('error', 'Não é possível excluir o último administrador ativo.');
            }
        }

        // Verificar se o usuário tem faturas associadas
        if ($user->invoices()->count() > 0) {
            return redirect()->route('users.index')
                ->with('error', 'Não é possível excluir um usuário que possui faturas associadas.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuário removido com sucesso!');
    }

    /**
     * Exibir o perfil do usuário atual.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $user = Auth::user();
        return view('users.profile', compact('user'));
    }

    /**
     * Atualizar o perfil do usuário atual.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return redirect()->route('profile')
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    /**
     * Alterar a senha do usuário atual.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verificar se a senha atual está correta
        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->route('profile')
                ->with('error', 'A senha atual está incorreta.')
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile')
            ->with('success', 'Senha alterada com sucesso!');
    }
}
