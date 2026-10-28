<?php

namespace App\Http\Controllers;

use App\Models\Provedor;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UniversidadeAdministracaoController extends Controller
{
    public function index(Request $request): View
    {
        $this->autorizarAdministrador($request);

        return view('universidade.administracao', [
            'utilizadores' => User::query()->with('provedor')->orderBy('name')->get(),
            'provedores' => Provedor::query()->withCount('utilizadores')->orderBy('nome')->get(),
        ]);
    }

    public function guardarUtilizador(Request $request): RedirectResponse
    {
        $this->autorizarAdministrador($request);
        $dados = $this->validarUtilizador($request);

        User::create($this->dadosUtilizador($dados));

        return back()->with('sucesso', 'Conta criada com sucesso.');
    }

    public function atualizarUtilizador(Request $request, User $user): RedirectResponse
    {
        $this->autorizarAdministrador($request);
        $dados = $this->validarUtilizador($request, $user);

        $ativo = $request->boolean('ativo');
        $vaiContinuarAdministrador = $dados['role_id'] === 'admin' && $ativo;
        if ($user->isAdmin() && $user->ativo && ! $vaiContinuarAdministrador
            && ! User::query()->where('role_id', 'admin')->where('ativo', true)->where('id', '!=', $user->id)->exists()) {
            return back()->withErrors([
                'utilizador' => 'Não é possível desativar ou rebaixar o último administrador ativo.',
            ]);
        }

        $user->update($this->dadosUtilizador($dados, $ativo));

        return back()->with('sucesso', 'Conta atualizada com sucesso.');
    }

    public function guardarProvedor(Request $request): RedirectResponse
    {
        $this->autorizarAdministrador($request);

        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        Provedor::create($dados + ['ativo' => true]);

        return back()->with('sucesso', 'Provedor criado com sucesso.');
    }

    public function atualizarProvedor(Request $request, Provedor $provedor): RedirectResponse
    {
        $this->autorizarAdministrador($request);

        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'ativo' => 'required|boolean',
        ]);

        $provedor->update($dados);

        return back()->with('sucesso', 'Provedor atualizado com sucesso.');
    }

    private function validarUtilizador(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'role_id' => ['required', Rule::in(['admin', 'provedor'])],
            'provedor_id' => [
                Rule::requiredIf($request->input('role_id') === 'provedor'),
                'nullable',
                Rule::exists('provedores', 'id')->where(fn ($query) => $query->where('ativo', true)),
            ],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:12', 'confirmed'],
            'ativo' => [$user ? 'required' : 'sometimes', 'boolean'],
        ]);
    }

    private function dadosUtilizador(array $dados, ?bool $ativo = null): array
    {
        $atributos = [
            'name' => $dados['name'],
            'email' => $dados['email'],
            'role_id' => $dados['role_id'],
            'provedor_id' => $dados['role_id'] === 'provedor' ? $dados['provedor_id'] : null,
        ];

        if (isset($dados['password']) && $dados['password'] !== '') {
            $atributos['password'] = $dados['password'];
        }

        if ($ativo !== null) {
            $atributos['ativo'] = $ativo;
        }

        return $atributos;
    }

    private function autorizarAdministrador(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403);
    }
}
