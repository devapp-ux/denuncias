<?php

namespace Tests\Feature;

use App\Mail\NotificacaoManifestacaoMail;
use App\Models\Evidencia;
use App\Models\Manifestacao;
use App\Models\OutboxEmail;
use App\Models\Provedor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ManifestacaoSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_submission_opens_a_printable_receipt(): void
    {
        $provedor = Provedor::create([
            'nome' => 'Provedor de teste',
            'email' => 'provedor@example.test',
            'ativo' => true,
        ]);

        $codigo = 'MAN-'.now()->year.'D0001';

        $response = $this->post(route('manifestacao.denuncia.store'), [
            'provedor_id' => $provedor->id,
            'confidencialidade' => '1',
            'confirmacao' => '1',
            'tipo_identificacao' => 'anonimo',
            'assunto' => 'Teste de submissão',
            'categoria' => 'fraude',
            'unidade_organica' => 'Unidade de teste',
            'descricao' => 'Descrição de teste.',
        ]);

        $response
            ->assertRedirect(route('manifestacao.carta'))
            ->assertSessionHas('sucesso', 'Denúncia submetida com sucesso.')
            ->assertSessionHas('manifestacao_id')
            ->assertSessionMissing('codigo');

        $senha = session('senha_acompanhamento');
        $manifestacao = Manifestacao::where('codigo', $codigo)->firstOrFail();
        $this->assertMatchesRegularExpression('/^[2-9A-HJ-NP-Z]{10}$/', $senha);
        $this->assertTrue(Hash::check($senha, $manifestacao->senha_acompanhamento_hash));
        $this->assertDatabaseHas('outbox_emails', [
            'manifestacao_id' => $manifestacao->id,
            'destinatario' => 'provedor@example.test',
            'status' => 'pendente',
        ]);

        $this->get(route('manifestacao.carta'))
            ->assertOk()
            ->assertSee('Denúncia submetida com sucesso.')
            ->assertSee($codigo)
            ->assertSee($senha)
            ->assertSee('Baixar comprovativo em PDF')
            ->assertSee('Copiar código e senha')
            ->assertSee('data-copy-credentials', false)
            ->assertSee('data-password=', false)
            ->assertSee(route('acompanhamento.index'), false);

        $pdf = $this->get(route('manifestacao.carta.pdf'));
        $pdf
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'attachment; filename=comprovativo-'.$codigo.'.pdf');
        $this->assertStringStartsWith('%PDF-', $pdf->getContent());

        $this->postJson(route('acompanhamento.consultar'), [
            'codigo' => $codigo,
            'senha' => $senha,
        ])->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('manifestacao.codigo', $codigo);

        $this->postJson(route('acompanhamento.mensagem', ['codigo' => $codigo]), [
            'senha' => $senha,
            'mensagem' => 'Pedido de esclarecimento.',
        ])->assertOk()
            ->assertJsonPath('ok', true);

        $this->assertDatabaseHas('mensagens', [
            'manifestacao_id' => $manifestacao->id,
            'mensagem' => 'Pedido de esclarecimento.',
        ]);
    }

    public function test_acompanhamento_route_renders_its_existing_view(): void
    {
        $this->get(route('acompanhamento.index'))
            ->assertOk()
            ->assertSee('id="codigo-manifestacao"', false)
            ->assertSee('id="senha-manifestacao"', false)
            ->assertSee('id="colar-credenciais"', false)
            ->assertSee('Colar')
            ->assertSee('id="colar-credenciais" class="ur-btn ur-btn-secondary" style="width:100%;margin-top:.75rem;" hidden', false)
            ->assertSee(route('acompanhamento.consultar'), false);
    }

    public function test_all_submission_forms_render_multiple_file_and_camera_inputs(): void
    {
        $provedor = new Provedor([
            'nome' => 'Provedor dinâmico',
            'email' => 'provedor@example.test',
            'ativo' => true,
        ]);
        $provedor->id = 42;
        $provedor->save();

        foreach ([
            'manifestacao.denuncia.create',
            'manifestacao.reclamacao.create',
            'manifestacao.sugestao.create',
        ] as $route) {
            $this->get(route($route))
                ->assertOk()
                ->assertSee('value="42"', false)
                ->assertSee('Provedor dinâmico')
                ->assertSee('name="evidencias[]"', false)
                ->assertSee('id="file-input"', false)
                ->assertSee('multiple', false)
                ->assertSee('id="camera-input"', false)
                ->assertSee('capture="environment"', false);
        }
    }

    public function test_validation_errors_are_visible_after_a_rejected_submission(): void
    {
        $response = $this
            ->from(route('manifestacao.denuncia.create'))
            ->post(route('manifestacao.denuncia.store'), []);

        $response->assertRedirect(route('manifestacao.denuncia.create'));

        $this->get(route('manifestacao.denuncia.create'))
            ->assertOk()
            ->assertSee('Não foi possível submeter a manifestação. Verifique estes campos:')
            ->assertSee('The provedor id field is required.');
    }

    public function test_tracking_rejects_an_incorrect_password_and_protects_messages(): void
    {
        $provedor = new Provedor([
            'nome' => 'Provedor de teste',
            'email' => 'provedor@example.test',
            'ativo' => true,
        ]);
        $provedor->id = 42;
        $provedor->save();

        $response = $this->post(route('manifestacao.denuncia.store'), [
            'provedor_id' => $provedor->id,
            'confidencialidade' => '1',
            'confirmacao' => '1',
            'tipo_identificacao' => 'anonimo',
            'assunto' => 'Teste',
            'categoria' => 'fraude',
            'unidade_organica' => 'Unidade de teste',
            'descricao' => 'Descrição de teste.',
        ]);

        $codigo = Manifestacao::query()->value('codigo');
        $this->assertNotEmpty($codigo);

        $this->postJson(route('acompanhamento.consultar'), [
            'codigo' => $codigo,
            'senha' => 'SENHAINVALIDA',
        ])->assertNotFound()
            ->assertJsonPath('ok', false);

        $this->postJson(route('acompanhamento.mensagem', ['codigo' => $codigo]), [
            'senha' => 'SENHAINVALIDA',
            'mensagem' => 'Mensagem sem autorização',
        ])->assertNotFound();

        $this->assertDatabaseCount('mensagens', 0);
    }

    public function test_legacy_submission_can_be_tracked_using_its_code_as_password(): void
    {
        $provedor = Provedor::create([
            'nome' => 'Provedor de teste',
            'ativo' => true,
        ]);

        $manifestacao = Manifestacao::create([
            'codigo' => 'MAN-2025D0001',
            'tipo' => 'denuncia',
            'provedor_id' => $provedor->id,
            'anonimo' => true,
            'confidencialidade' => true,
            'confirmacao' => true,
            'estado' => 'pendente',
        ]);

        $this->postJson(route('acompanhamento.consultar'), [
            'codigo' => $manifestacao->codigo,
            'senha' => $manifestacao->codigo,
        ])->assertOk()
            ->assertJsonPath('manifestacao.codigo', $manifestacao->codigo);
    }

    public function test_outbox_sends_notification_to_the_selected_provider(): void
    {
        Mail::fake();
        config(['mail.default' => 'smtp']);

        $provedor = Provedor::create([
            'id' => 42,
            'nome' => 'Provedor de teste',
            'email' => 'provedor@example.test',
            'ativo' => true,
        ]);

        $this->post(route('manifestacao.denuncia.store'), [
            'provedor_id' => $provedor->id,
            'confidencialidade' => '1',
            'confirmacao' => '1',
            'tipo_identificacao' => 'anonimo',
            'assunto' => 'Teste de notificação',
            'categoria' => 'fraude',
            'unidade_organica' => 'Unidade de teste',
            'descricao' => 'Descrição de teste.',
        ])->assertRedirect(route('manifestacao.carta'));

        $outboxEmail = OutboxEmail::query()->firstOrFail();

        Mail::assertSent(NotificacaoManifestacaoMail::class, fn ($mail) => $mail->hasTo('provedor@example.test')
        && $mail->dados['codigo'] === $outboxEmail->payload['codigo']);

        $this->assertDatabaseHas('outbox_emails', [
            'id' => $outboxEmail->id,
            'status' => 'processado',
        ]);

        $this->artisan('outbox:processar')->assertSuccessful();

        Mail::assertSent(NotificacaoManifestacaoMail::class, fn ($mail) => $mail->hasTo('provedor@example.test')
        && $mail->dados['codigo'] === $outboxEmail->payload['codigo']);

        $this->assertDatabaseHas('outbox_emails', [
            'id' => $outboxEmail->id,
            'status' => 'processado',
        ]);
    }

    public function test_each_manifestation_type_creates_an_outbox_notification(): void
    {
        $provedor = Provedor::create([
            'nome' => 'Provedor de teste',
            'email' => 'provedor@example.test',
            'ativo' => true,
        ]);

        $submissoes = [
            [
                'route' => 'manifestacao.denuncia.store',
                'dados' => [
                    'assunto' => 'Denúncia',
                    'categoria' => 'fraude',
                    'unidade_organica' => 'Unidade de teste',
                    'descricao' => 'Descrição da denúncia.',
                ],
            ],
            [
                'route' => 'manifestacao.reclamacao.store',
                'dados' => [
                    'assunto' => 'Reclamação',
                    'categoria' => 'serviço',
                    'area_servico' => 'Atendimento',
                    'descricao' => 'Descrição da reclamação.',
                ],
            ],
            [
                'route' => 'manifestacao.sugestao.store',
                'dados' => [
                    'titulo' => 'Sugestão',
                    'area_relacionada' => 'Atendimento',
                    'sugestao' => 'Descrição da sugestão.',
                ],
            ],
        ];

        foreach ($submissoes as $submissao) {
            $this->post(route($submissao['route']), $submissao['dados'] + [
                'provedor_id' => $provedor->id,
                'confidencialidade' => '1',
                'confirmacao' => '1',
                'tipo_identificacao' => 'anonimo',
            ])->assertRedirect(route('manifestacao.carta'));
        }

        $this->assertDatabaseCount('manifestacoes', 3);
        $this->assertDatabaseCount('outbox_emails', 3);
        $this->assertSame(3, OutboxEmail::query()->where('status', 'pendente')->count());
    }

    public function test_submission_without_provider_email_still_creates_pending_notification(): void
    {
        Mail::fake();
        config(['mail.default' => 'smtp']);

        $provedor = Provedor::create([
            'nome' => 'Provedor sem e-mail',
            'ativo' => true,
        ]);

        $this->from(route('manifestacao.denuncia.create'))
            ->post(route('manifestacao.denuncia.store'), [
                'provedor_id' => $provedor->id,
                'confidencialidade' => '1',
                'confirmacao' => '1',
                'tipo_identificacao' => 'anonimo',
                'assunto' => 'Teste',
                'categoria' => 'fraude',
                'unidade_organica' => 'Unidade de teste',
                'descricao' => 'Descrição de teste.',
            ])
            ->assertRedirect(route('manifestacao.carta'));

        $this->assertDatabaseCount('manifestacoes', 1);
        $this->assertDatabaseHas('outbox_emails', [
            'destinatario' => null,
            'status' => 'pendente',
        ]);

        $this->artisan('outbox:processar')->assertSuccessful();

        Mail::assertNothingSent();
        $this->assertDatabaseHas('outbox_emails', [
            'destinatario' => null,
            'status' => 'pendente',
            'tentativas' => 0,
        ]);
    }

    public function test_outbox_does_not_mark_notifications_sent_with_log_mailer(): void
    {
        $provedor = Provedor::create([
            'nome' => 'Provedor de teste',
            'email' => 'provedor@example.test',
            'ativo' => true,
        ]);

        $this->post(route('manifestacao.denuncia.store'), [
            'provedor_id' => $provedor->id,
            'confidencialidade' => '1',
            'confirmacao' => '1',
            'tipo_identificacao' => 'anonimo',
            'assunto' => 'Teste de transporte',
            'categoria' => 'fraude',
            'unidade_organica' => 'Unidade de teste',
            'descricao' => 'Descrição de teste.',
        ])->assertRedirect(route('manifestacao.carta'));

        config(['mail.default' => 'log']);

        $this->artisan('outbox:processar')->assertFailed();

        $this->assertDatabaseHas('outbox_emails', [
            'destinatario' => 'provedor@example.test',
            'status' => 'pendente',
            'tentativas' => 0,
            'ultimo_erro' => 'MAIL_MAILER está configurado para um transporte que não entrega e-mails.',
        ]);
    }

    public function test_outbox_trims_provider_email_before_sending(): void
    {
        Mail::fake();
        config(['mail.default' => 'smtp']);

        $provedor = Provedor::create([
            'nome' => 'Provedor de teste',
            'email' => " provedor@example.test\r\n",
            'ativo' => true,
        ]);

        $this->post(route('manifestacao.denuncia.store'), [
            'provedor_id' => $provedor->id,
            'confidencialidade' => '1',
            'confirmacao' => '1',
            'tipo_identificacao' => 'anonimo',
            'assunto' => 'Teste de endereço',
            'categoria' => 'fraude',
            'unidade_organica' => 'Unidade de teste',
            'descricao' => 'Descrição de teste.',
        ])->assertRedirect(route('manifestacao.carta'));

        $this->artisan('outbox:processar')->assertSuccessful();

        Mail::assertSent(NotificacaoManifestacaoMail::class, fn ($mail) => $mail->hasTo('provedor@example.test'));
    }

    public function test_submission_accepts_seven_camera_photos_and_persists_them(): void
    {
        Storage::fake('public');

        $provedor = Provedor::create([
            'nome' => 'Provedor de teste',
            'email' => 'provedor@example.test',
            'ativo' => true,
        ]);

        $fotos = [];
        for ($i = 1; $i <= 7; $i++) {
            $fotos[] = UploadedFile::fake()->image("foto-{$i}.jpg")->size(128);
        }

        $this->post(route('manifestacao.denuncia.store'), [
            'provedor_id' => $provedor->id,
            'confidencialidade' => '1',
            'confirmacao' => '1',
            'tipo_identificacao' => 'anonimo',
            'assunto' => 'Teste com fotografias',
            'categoria' => 'fraude',
            'unidade_organica' => 'Unidade de teste',
            'descricao' => 'Descrição de teste.',
            'evidencias' => $fotos,
        ])->assertRedirect(route('manifestacao.carta'));

        $this->assertDatabaseCount('evidencias', 7);

        foreach (Evidencia::query()->get() as $evidencia) {
            Storage::disk('public')->assertExists($evidencia->caminho);
        }
    }

    public function test_suggestion_submission_stores_multiple_files_from_the_shared_attachment_field(): void
    {
        Storage::fake('public');

        $provedor = Provedor::create([
            'nome' => 'Provedor de teste',
            'email' => 'provedor@example.test',
            'ativo' => true,
        ]);

        $this->post(route('manifestacao.sugestao.store'), [
            'provedor_id' => $provedor->id,
            'confidencialidade' => '1',
            'confirmacao' => '1',
            'tipo_identificacao' => 'anonimo',
            'titulo' => 'Sugestão de teste',
            'area_relacionada' => 'Atendimento',
            'sugestao' => 'Descrição da sugestão.',
            'evidencias' => [
                UploadedFile::fake()->image('camera-1.jpg'),
                UploadedFile::fake()->image('camera-2.jpg'),
            ],
        ])->assertRedirect(route('manifestacao.carta'));

        $this->assertDatabaseCount('evidencias', 2);
    }

    public function test_submission_rejects_more_than_seven_files_and_files_over_twenty_megabytes(): void
    {
        $provedor = Provedor::create([
            'nome' => 'Provedor de teste',
            'email' => 'provedor@example.test',
            'ativo' => true,
        ]);

        $dados = [
            'provedor_id' => $provedor->id,
            'confidencialidade' => '1',
            'confirmacao' => '1',
            'tipo_identificacao' => 'anonimo',
            'assunto' => 'Teste de limites',
            'categoria' => 'fraude',
            'unidade_organica' => 'Unidade de teste',
            'descricao' => 'Descrição de teste.',
        ];

        $oitoFicheiros = [];
        for ($i = 1; $i <= 8; $i++) {
            $oitoFicheiros[] = UploadedFile::fake()->create("ficheiro-{$i}.pdf", 1, 'application/pdf');
        }

        $this->from(route('manifestacao.denuncia.create'))
            ->post(route('manifestacao.denuncia.store'), $dados + ['evidencias' => $oitoFicheiros])
            ->assertRedirect(route('manifestacao.denuncia.create'))
            ->assertSessionHasErrors('evidencias');

        $this->from(route('manifestacao.denuncia.create'))
            ->post(route('manifestacao.denuncia.store'), $dados + [
                'evidencias' => [UploadedFile::fake()->create('grande.pdf', 20481, 'application/pdf')],
            ])
            ->assertRedirect(route('manifestacao.denuncia.create'))
            ->assertSessionHasErrors('evidencias.0');

        $this->assertDatabaseCount('manifestacoes', 0);
    }
}
