<?php

namespace Tests\Feature;

use App\Models\Manifestacao;
use App\Models\Mensagem;
use App\Models\Provedor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UniversidadeAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_university_dashboard_requires_an_internal_login(): void
    {
        $this->get(route('universidade.dashboard'))
            ->assertRedirect(route('universidade.login'));

        $this->get(route('universidade.login'))
            ->assertOk()
            ->assertSee('Acesso institucional');
    }

    public function test_provider_only_sees_and_updates_manifestations_for_its_service(): void
    {
        $provedor = Provedor::create([
            'nome' => 'Provedor do estudante',
            'email' => 'estudante@example.test',
            'ativo' => true,
        ]);
        $outroProvedor = Provedor::create([
            'nome' => 'Outro serviço',
            'email' => 'outro@example.test',
            'ativo' => true,
        ]);
        $user = User::factory()->create([
            'role_id' => 'provedor',
            'provedor_id' => $provedor->id,
            'ativo' => true,
        ]);
        $suaManifestacao = $this->criarManifestacao($provedor);
        $manifestacaoAlheia = $this->criarManifestacao($outroProvedor);

        $this->actingAs($user)
            ->get(route('universidade.dashboard'))
            ->assertOk()
            ->assertSee($suaManifestacao->codigo)
            ->assertDontSee($manifestacaoAlheia->codigo);

        $this->get(route('universidade.manifestacoes.show', $manifestacaoAlheia))
            ->assertNotFound();

        $this->patch(route('universidade.manifestacoes.atualizar', $suaManifestacao), [
            'estado' => 'em_analise',
            'observacao' => 'Análise iniciada.',
        ])->assertRedirect(route('universidade.manifestacoes.show', $suaManifestacao));

        $this->assertDatabaseHas('manifestacoes', [
            'id' => $suaManifestacao->id,
            'estado' => 'em_analise',
        ]);
        $this->assertDatabaseHas('encaminhamentos', [
            'manifestacao_id' => $suaManifestacao->id,
            'estado_anterior' => 'pendente',
            'estado_novo' => 'em_analise',
            'observacao' => 'Análise iniciada.',
            'user_id' => $user->id,
        ]);

        $this->get(route('universidade.manifestacoes.show', $suaManifestacao))
            ->assertOk()
            ->assertSee('Análise iniciada.')
            ->assertSee('Histórico do processo');

        $this->postJson(route('acompanhamento.consultar'), [
            'codigo' => $suaManifestacao->codigo,
            'senha' => $suaManifestacao->codigo,
        ])->assertOk()
            ->assertJsonPath('timeline.1.titulo', 'Em análise')
            ->assertJsonMissing(['desc' => 'Análise iniciada.']);
    }

    public function test_university_can_reply_in_manifestation_chat_and_message_is_visible_publicly(): void
    {
        $provedor = Provedor::create(['nome' => 'Provedor', 'ativo' => true]);
        $user = User::factory()->create([
            'role_id' => 'provedor',
            'provedor_id' => $provedor->id,
            'ativo' => true,
        ]);
        $manifestacao = $this->criarManifestacao($provedor);

        Mensagem::create([
            'manifestacao_id' => $manifestacao->id,
            'autor' => 'utilizador',
            'mensagem' => 'Solicito informação sobre o processo.',
            'lida' => false,
        ]);

        $this->actingAs($user)
            ->get(route('universidade.manifestacoes.show', $manifestacao))
            ->assertOk()
            ->assertSee('Solicito informação sobre o processo.')
            ->assertSee('1 mensagem(ns) nova(s)');

        $this->assertDatabaseHas('mensagens', [
            'manifestacao_id' => $manifestacao->id,
            'autor' => 'utilizador',
            'lida' => true,
        ]);

        $this->post(route('universidade.manifestacoes.mensagens.guardar', $manifestacao), [
            'mensagem' => 'A sua manifestação encontra-se em análise.',
        ])->assertRedirect(route('universidade.manifestacoes.show', $manifestacao));

        $this->assertDatabaseHas('mensagens', [
            'manifestacao_id' => $manifestacao->id,
            'autor' => 'instituicao',
            'mensagem' => 'A sua manifestação encontra-se em análise.',
        ]);

        $this->postJson(route('acompanhamento.consultar'), [
            'codigo' => $manifestacao->codigo,
            'senha' => $manifestacao->codigo,
        ])->assertOk()
            ->assertJsonPath('mensagens.1.autor', 'instituicao')
            ->assertJsonPath('mensagens.1.mensagem', 'A sua manifestação encontra-se em análise.');
    }

    public function test_manifestation_live_updates_are_scoped_and_return_new_messages_and_history(): void
    {
        $provedor = Provedor::create(['nome' => 'Provedor', 'ativo' => true]);
        $outroProvedor = Provedor::create(['nome' => 'Outro serviço', 'ativo' => true]);
        $user = User::factory()->create([
            'role_id' => 'provedor',
            'provedor_id' => $provedor->id,
            'ativo' => true,
        ]);
        $manifestacao = $this->criarManifestacao($provedor);
        $manifestacaoAlheia = $this->criarManifestacao($outroProvedor);

        $this->actingAs($user)
            ->get(route('universidade.manifestacoes.sincronizar', $manifestacaoAlheia))
            ->assertNotFound();

        $cursor = [
            'versao' => 'versao-antiga',
            'mensagem_apos' => 0,
            'evento_apos' => 0,
        ];
        Mensagem::create([
            'manifestacao_id' => $manifestacao->id,
            'autor' => 'utilizador',
            'mensagem' => 'Nova mensagem do manifestante.',
            'lida' => false,
        ]);

        $this->actingAs($user)
            ->getJson(route('universidade.manifestacoes.sincronizar', [$manifestacao, ...$cursor]))
            ->assertOk()
            ->assertJsonPath('mensagens.0.mensagem', 'Nova mensagem do manifestante.')
            ->assertJsonPath('eventos', []);

        $this->assertDatabaseHas('mensagens', [
            'manifestacao_id' => $manifestacao->id,
            'autor' => 'utilizador',
            'lida' => true,
        ]);

        $versaoAtual = $manifestacao->fresh()->updated_at->toISOString();
        $ultimaMensagemId = $manifestacao->mensagens()->max('id');
        $this->get(route('universidade.manifestacoes.sincronizar', $manifestacao).'?'.http_build_query([
            'versao' => $versaoAtual,
            'mensagem_apos' => $ultimaMensagemId,
            'evento_apos' => 0,
        ]))->assertNoContent();
    }

    public function test_dashboard_sync_returns_updates_only_when_the_version_changes(): void
    {
        $provedor = Provedor::create(['nome' => 'Provedor', 'ativo' => true]);
        $user = User::factory()->create([
            'role_id' => 'provedor',
            'provedor_id' => $provedor->id,
            'ativo' => true,
        ]);
        $manifestacao = $this->criarManifestacao($provedor);

        $this->actingAs($user)
            ->getJson(route('universidade.dashboard.sincronizar', ['versao' => 'versao-antiga']))
            ->assertOk()
            ->assertJsonPath('html', fn (string $html) => str_contains($html, $manifestacao->codigo));

        $response = $this->get(route('universidade.dashboard'));
        preg_match('/data-versao="([^"]+)"/', $response->getContent(), $matches);

        $this->get(route('universidade.dashboard.sincronizar', ['versao' => $matches[1]]))
            ->assertNoContent();
    }

    public function test_dashboard_pagination_uses_portuguese_labels_and_summary(): void
    {
        $provedor = Provedor::create(['nome' => 'Provedor', 'ativo' => true]);
        $user = User::factory()->create([
            'role_id' => 'provedor',
            'provedor_id' => $provedor->id,
            'ativo' => true,
        ]);

        foreach (range(1, 18) as $numero) {
            $this->criarManifestacao($provedor);
        }

        $this->actingAs($user)
            ->get(route('universidade.dashboard'))
            ->assertOk()
            ->assertSeeText('A mostrar 1–15 de 18 resultados')
            ->assertSee('Anterior')
            ->assertSee('Seguinte')
            ->assertDontSee('Showing')
            ->assertDontSee('Previous')
            ->assertDontSee('Next »');
    }

    public function test_public_tracking_sync_returns_new_messages_and_status_without_internal_notes(): void
    {
        $provedor = Provedor::create(['nome' => 'Provedor', 'ativo' => true]);
        $manifestacao = $this->criarManifestacao($provedor);
        $versaoAnterior = $manifestacao->updated_at->toISOString();
        $manifestacao->update(['estado' => 'em_analise']);
        Mensagem::create([
            'manifestacao_id' => $manifestacao->id,
            'autor' => 'instituicao',
            'mensagem' => 'Estamos a analisar a sua manifestação.',
            'lida' => false,
        ]);

        $this->postJson(route('acompanhamento.sincronizar'), [
            'codigo' => $manifestacao->codigo,
            'senha' => $manifestacao->codigo,
            'versao' => $versaoAnterior,
            'mensagem_apos' => 0,
        ])->assertOk()
            ->assertJsonPath('manifestacao.estado', 'em_analise')
            ->assertJsonPath('mensagens.0.mensagem', 'Estamos a analisar a sua manifestação.')
            ->assertJsonMissing(['observacao' => 'Nota interna']);

        $this->postJson(route('acompanhamento.sincronizar'), [
            'codigo' => $manifestacao->codigo,
            'senha' => 'senha-incorreta',
            'versao' => $versaoAnterior,
            'mensagem_apos' => 0,
        ])->assertNotFound();
    }

    public function test_university_can_update_a_manifestation_without_a_full_page_redirect(): void
    {
        $provedor = Provedor::create(['nome' => 'Provedor', 'ativo' => true]);
        $user = User::factory()->create([
            'role_id' => 'provedor',
            'provedor_id' => $provedor->id,
            'ativo' => true,
        ]);
        $manifestacao = $this->criarManifestacao($provedor);

        $this->actingAs($user)
            ->post(route('universidade.manifestacoes.atualizar', $manifestacao), [
                '_method' => 'PATCH',
                'estado' => 'em_analise',
                'observacao' => 'Análise iniciada.',
            ], ['Accept' => 'application/json'])->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('estado', 'em_analise')
            ->assertJsonPath('estado_label', 'Em análise');

        $this->assertDatabaseHas('manifestacoes', [
            'id' => $manifestacao->id,
            'estado' => 'em_analise',
        ]);
    }

    public function test_provider_cannot_reply_to_manifestation_assigned_to_another_service(): void
    {
        $provedor = Provedor::create(['nome' => 'Provedor A', 'ativo' => true]);
        $outroProvedor = Provedor::create(['nome' => 'Provedor B', 'ativo' => true]);
        $user = User::factory()->create([
            'role_id' => 'provedor',
            'provedor_id' => $provedor->id,
            'ativo' => true,
        ]);
        $manifestacao = $this->criarManifestacao($outroProvedor);

        $this->actingAs($user)
            ->post(route('universidade.manifestacoes.mensagens.guardar', $manifestacao), [
                'mensagem' => 'Mensagem sem autorização.',
            ])->assertNotFound();

        $this->assertDatabaseMissing('mensagens', [
            'manifestacao_id' => $manifestacao->id,
            'mensagem' => 'Mensagem sem autorização.',
        ]);
    }

    public function test_admin_can_reassign_and_view_every_manifestation(): void
    {
        $provedor = Provedor::create([
            'nome' => 'Serviço atual',
            'ativo' => true,
        ]);
        $novoProvedor = Provedor::create([
            'nome' => 'Novo serviço',
            'ativo' => true,
        ]);
        $admin = User::factory()->create([
            'role_id' => 'admin',
            'provedor_id' => null,
            'ativo' => true,
        ]);
        $manifestacao = $this->criarManifestacao($provedor);

        $this->actingAs($admin)
            ->get(route('universidade.dashboard'))
            ->assertOk()
            ->assertSee($manifestacao->codigo);

        $this->patch(route('universidade.manifestacoes.atualizar', $manifestacao), [
            'estado' => 'encaminhada',
            'provedor_id' => $novoProvedor->id,
            'observacao' => 'Reencaminhada pela administração.',
        ])->assertRedirect(route('universidade.manifestacoes.show', $manifestacao));

        $this->assertDatabaseHas('manifestacoes', [
            'id' => $manifestacao->id,
            'estado' => 'encaminhada',
            'provedor_id' => $novoProvedor->id,
        ]);
        $this->assertDatabaseHas('encaminhamentos', [
            'manifestacao_id' => $manifestacao->id,
            'provedor_id' => $novoProvedor->id,
            'estado_anterior' => 'pendente',
            'estado_novo' => 'encaminhada',
            'user_id' => $admin->id,
        ]);

        $this->get(route('universidade.administracao.index'))->assertOk();
    }

    public function test_admin_can_open_manifestation_assigned_to_inactive_provider(): void
    {
        $provedor = Provedor::create([
            'nome' => 'Serviço desativado',
            'ativo' => false,
        ]);
        $admin = User::factory()->create([
            'role_id' => 'admin',
            'provedor_id' => null,
            'ativo' => true,
        ]);
        $manifestacao = $this->criarManifestacao($provedor);

        $this->actingAs($admin)
            ->get(route('universidade.manifestacoes.show', $manifestacao))
            ->assertOk()
            ->assertSee($manifestacao->codigo)
            ->assertSee('Serviço desativado');
    }

    public function test_provider_cannot_open_administration_or_create_accounts(): void
    {
        $provedor = Provedor::create(['nome' => 'Provedor', 'ativo' => true]);
        $user = User::factory()->create([
            'role_id' => 'provedor',
            'provedor_id' => $provedor->id,
            'ativo' => true,
        ]);

        $this->actingAs($user)
            ->get(route('universidade.administracao.index'))
            ->assertForbidden();

        $this->post(route('universidade.administracao.utilizadores.guardar'), [
            'name' => 'Conta indevida',
            'email' => 'indevida@example.test',
            'role_id' => 'admin',
            'password' => 'senha-muito-forte',
            'password_confirmation' => 'senha-muito-forte',
        ])->assertForbidden();
    }

    public function test_inactive_and_unassigned_users_cannot_enter_the_internal_area(): void
    {
        $user = User::factory()->create([
            'role_id' => 'provedor',
            'provedor_id' => null,
            'ativo' => true,
            'password' => Hash::make('palavra-passe-segura'),
        ]);

        $this->post(route('universidade.login.store'), [
            'email' => $user->email,
            'password' => 'palavra-passe-segura',
        ])->assertSessionHasErrors('email');

        $user->update(['role_id' => 'admin', 'ativo' => false]);

        $this->post(route('universidade.login.store'), [
            'email' => $user->email,
            'password' => 'palavra-passe-segura',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_can_create_provider_accounts_and_manage_providers(): void
    {
        $admin = User::factory()->create([
            'role_id' => 'admin',
            'provedor_id' => null,
            'ativo' => true,
        ]);
        $provedor = Provedor::create(['nome' => 'Serviço ativo', 'ativo' => true]);

        $this->actingAs($admin)
            ->post(route('universidade.administracao.utilizadores.guardar'), [
                'name' => 'Utilizador do serviço',
                'email' => 'servico@example.test',
                'role_id' => 'provedor',
                'provedor_id' => $provedor->id,
                'password' => 'palavra-passe-forte',
                'password_confirmation' => 'palavra-passe-forte',
            ])->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'servico@example.test',
            'role_id' => 'provedor',
            'provedor_id' => $provedor->id,
            'ativo' => true,
        ]);

        $this->patch(route('universidade.administracao.provedores.atualizar', $provedor), [
            'nome' => 'Serviço atualizado',
            'email' => 'atualizado@example.test',
            'ativo' => 0,
        ])->assertRedirect();

        $this->assertDatabaseHas('provedores', [
            'id' => $provedor->id,
            'nome' => 'Serviço atualizado',
            'ativo' => false,
        ]);
    }

    public function test_last_active_admin_cannot_deactivate_own_account(): void
    {
        $admin = User::factory()->create([
            'role_id' => 'admin',
            'provedor_id' => null,
            'ativo' => true,
        ]);

        $this->actingAs($admin)
            ->from(route('universidade.administracao.index'))
            ->patch(route('universidade.administracao.utilizadores.atualizar', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'role_id' => 'admin',
                'provedor_id' => '',
                'password' => '',
                'password_confirmation' => '',
                'ativo' => 0,
            ])->assertRedirect(route('universidade.administracao.index'))
            ->assertSessionHasErrors('utilizador');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'ativo' => true,
            'role_id' => 'admin',
        ]);
    }

    private function criarManifestacao(Provedor $provedor): Manifestacao
    {
        return Manifestacao::create([
            'codigo' => 'MAN-'.now()->year.'D'.str_pad((string) (Manifestacao::count() + 1), 4, '0', STR_PAD_LEFT),
            'tipo' => 'denuncia',
            'provedor_id' => $provedor->id,
            'anonimo' => true,
            'confidencialidade' => true,
            'confirmacao' => true,
            'estado' => 'pendente',
        ]);
    }
}
