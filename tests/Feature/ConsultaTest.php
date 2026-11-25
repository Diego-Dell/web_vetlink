<?php

namespace Tests\Feature;

use App\Support\Dosis;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ConsultaTest extends TestCase
{
    private const FICHA = '11111111-1111-1111-1111-111111111111';

    private const GRANJA = '22222222-2222-2222-2222-222222222222';

    private const TRAT = '33333333-3333-3333-3333-333333333333';

    protected function setUp(): void
    {
        parent::setUp();
        config(['vetlink.supabase_url' => 'https://supabase.prueba', 'vetlink.supabase_key' => 'clave']);
    }

    private function conSesion(): static
    {
        return $this->withSession(['vetlink' => ['token' => 't', 'refresh' => 'r', 'vence' => time() + 600, 'cuenta' => 'c1', 'correo' => 'sofia@ejemplo.com', 'nombre' => 'Sofía Rojas']]);
    }

    private function simularSupabase(): void
    {
        Http::fake([
            'supabase.prueba/rest/v1/vinculaciones*' => Http::response([['rol' => 'dueno', 'granjas' => ['id' => self::GRANJA, 'nombre' => 'Los Pinos', 'confirmada' => true]]]),
            'supabase.prueba/rest/v1/fichas_bovinas*' => Http::response([['id' => self::FICHA, 'granja_id' => self::GRANJA, 'identificacion' => 'BO-1042', 'nombre' => 'Luna', 'raza' => 'Holando', 'etapa' => 'vaca', 'nacimiento' => null, 'fecha_baja' => null, 'motivo_baja' => null]]),
            'supabase.prueba/rest/v1/tratamientos*' => Http::response([['id' => self::TRAT, 'ficha_id' => self::FICHA, 'medicamento' => 'Cefalexina', 'dosis' => 5, 'unidad' => 'ml', 'via' => 'Intramuscular', 'frecuencia' => '24 h', 'duracion' => '10 días', 'inicio' => now()->toDateString(), 'fecha_fin' => null, 'anulado' => false, 'retiro' => null]]),
            'supabase.prueba/rest/v1/aplicaciones*' => Http::response([['tratamiento_id' => self::TRAT, 'fecha_hora_real' => '2026-11-22T12:00:00Z', 'dosis' => 5, 'unidad' => 'ml', 'observacion' => null, 'tipo' => 'tratamiento'], ['tratamiento_id' => self::TRAT, 'fecha_hora_real' => '2026-11-22T12:00:00Z', 'dosis' => 5, 'unidad' => 'ml', 'observacion' => null, 'tipo' => 'tratamiento']]),
            'supabase.prueba/rest/v1/prevenciones*' => Http::response([]),
            'supabase.prueba/rest/v1/atenciones_clinicas*' => Http::response([['fecha' => '2026-11-22T10:00:00Z', 'tipo' => 'diagnostico', 'detalle' => 'Mastitis leve']]),
        ]);
        Http::preventStrayRequests();
    }

    public function test_la_landing_es_publica_y_lleva_a_iniciar_sesion(): void
    {
        $this->get('/')->assertOk()->assertSee('El cuidado de tu ganado')->assertSee(route('login'));
        $this->get('/login')->assertOk()->assertSee('Bienvenido a VetLink')->assertDontSee('Crear cuenta');
    }

    public function test_sin_cuenta_no_se_ven_datos(): void
    {
        foreach (['/app', '/app/ganado', '/app/reportes', '/app/perfil', '/app/ganado/'.self::FICHA] as $ruta) {
            $this->get($ruta)->assertRedirect('/login');
        }
    }

    public function test_la_web_no_acepta_escrituras(): void
    {
        foreach (['post', 'put', 'patch', 'delete'] as $metodo) {
            $this->conSesion()->{$metodo}('/app/ganado')->assertStatus(405);
        }
    }

    public function test_lee_supabase_directo_solo_con_get_y_muestra_el_contador(): void
    {
        $this->simularSupabase();
        $this->conSesion()->get('/app')->assertOk()
            ->assertSee('Los Pinos')->assertSee('Número de aplicaciones: 2/10')->assertSee('Faltan 8');
        Http::assertSent(fn ($p) => str_starts_with($p->url(), 'https://supabase.prueba/rest/v1/') && $p->hasHeader('apikey', 'clave') && $p->hasHeader('Authorization', 'Bearer t'));
        Http::assertNotSent(fn ($p) => $p->method() !== 'GET');
        Http::assertNotSent(fn ($p) => str_contains($p->url(), 'onrender.com'));
    }

    public function test_ganado_y_ficha_en_estilo_movil(): void
    {
        $this->simularSupabase();
        $this->conSesion()->get('/app/ganado')->assertOk()->assertSee('Gestionar ganado')->assertSee('BO-1042 · Luna')->assertSee('class="barra"', false);
        $this->conSesion()->get('/app/ganado/'.self::FICHA)->assertOk()->assertSee('Cefalexina')->assertSee('5 ml');
    }

    public function test_reportes_y_perfil_con_menu_lateral_y_barra_inferior(): void
    {
        $this->simularSupabase();
        $this->conSesion()->get('/app/reportes?periodo=mes')->assertOk()->assertSee('Aplicaciones por semana')->assertSee('class="lateral"', false)->assertSee('class="barra"', false);
        $this->conSesion()->get('/app/perfil')->assertOk()->assertSee('Mis granjas')->assertSee('Los Pinos');
    }

    public function test_filtros_invalidos_se_rechazan(): void
    {
        $this->simularSupabase();
        $this->conSesion()->get('/app/ganado?estado=borrar')->assertRedirect();
        $this->conSesion()->get('/app/reportes?periodo=siglo')->assertRedirect();
    }

    public function test_credenciales_incorrectas_y_correo_invalido(): void
    {
        Http::fake(['supabase.prueba/*' => Http::response(['error' => 'invalid_grant'], 400)]);
        $this->from('/login')->post('/login', ['correo' => 'sofia@ejemplo.com', 'contrasena' => 'mal'])
            ->assertRedirect('/login')->assertSessionHasErrors('correo');
        $this->from('/login')->post('/login', ['correo' => 'no-es-correo', 'contrasena' => 'x'])
            ->assertSessionHasErrors('correo');
        $this->assertNull(session('vetlink'));
    }

    public function test_token_rechazado_vuelve_a_iniciar_sesion(): void
    {
        Http::fake(['supabase.prueba/*' => Http::response([], 401)]);
        $this->conSesion()->get('/app')->assertRedirect('/login');
    }

    public function test_total_de_dosis(): void
    {
        $this->assertSame(10, Dosis::total('24 h', '10 días'));
        $this->assertSame(3, Dosis::total('48 h', '6 días'));
        $this->assertSame(1, Dosis::total('única', null));
        $this->assertNull(Dosis::total('según evolución', '5 días'));
        $this->assertSame('2,5', Dosis::numero(2.5));
        $this->assertSame('5', Dosis::numero('5.000'));
    }
}
