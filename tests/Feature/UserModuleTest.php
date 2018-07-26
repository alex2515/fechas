<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserModuleTest extends TestCase
{
	use RefreshDatabase;
    /** @test     */
    function lista_de_usuarios()
    {
    	$this->withoutExceptionHandling();
        factory(User::class)->create([
            'name' => 'Joel'
        ]);

        factory(User::class)->create([
            'name' => 'Ellie'
        ]);

        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('Listado de usuarios')
            ->assertSee('Joel')
            ->assertSee('Ellie');
    }

    /** @test */
    function lista_usuarios_vacio()
    {
        $this->get('/usuarios')
        ->assertStatus(200)
        ->assertSee('No hay usuarios registrados');
    }

    /** @test */
    function detalles_del_usuario()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create(['name' => 'Duilio']);
        $this->get("/usuarios/{$user->id}")
            ->assertStatus(200)
            ->assertSee('Mostrando detalles del Usuarios')
            ->assertSee($user->name);
    }

    /** @test */
    function mostrar_error_404_si_usuario_no_existe()
    {
        $this->get('/usuarios/999')
        ->assertStatus(404)
        ->assertSee('Página no encontrada');
    }


    /** @test */
    function mostrar_pagina_nuevo_usuario()
    {
        $this->withoutExceptionHandling();

        $this->get('/usuarios/nuevo')
        ->assertStatus(200)
        ->assertSee('Nuevo Usuario');
    }

    /** @test */
    function crear_usuario()
    {
        $this->withoutExceptionHandling();
        $this->post('/usuarios/', [
            'name' => 'Duilio',
            'email' => 'duilio@styde.net',
            'password' => '123456'
        ])->assertRedirect('/usuarios');

        $this->assertCredentials([
            'name' => 'Duilio',
            'email' => 'duilio@styde.net',
            'password' => '123456'
        ]);
    }

    /** @test */
    function el_nombre_es_obligatorio() 
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios', [
                'name' => '',
                'email' => 'duilio@styde.net',
                'password' => '123456'
            ])
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['name' => 'El campo nombre es obligatorio']);

        $this->assertEquals(0, User::count());
    }

    /** @test */
    function el_email_es_obligatorio()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', [
                'name' => 'Duilio',
                'email' => '',
                'password' => '123456'
            ])
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['email']);

        $this->assertEquals(0, User::count());
    }

    /** @test */
    function el_email_no_es_valido()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', [
                'name' => 'Duilio',
                'email' => 'correo-no-valido',
                'password' => '123456'
            ])
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['email']);

        $this->assertEquals(0, User::count());
    }

    /** @test */
    function el_password_es_obligatorio()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', [
                'name' => 'Duilio',
                'email' => 'duilio@styde.net',
                'password' => ''
            ])
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['password']);

        $this->assertEquals(0, User::count());
    }

    /** @test */
    function mostrar_pagina_editar_usuario()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();

        $this->get("/usuarios/{$user->id}/edit")
            ->assertStatus(200)
            ->assertViewIs('user.edit')
            ->assertSee('Editar Usuario')
            ->assertViewHas('user', function ($viewUser) use ($user){
                return $viewUser->id === $user->id;
            });
    }

    /** @test */
    function actualizando_usuario()
    {
        $user = factory(User::class)->create();

        //$this->withoutExceptionHandling();

        $this->put("/usuarios/{$user->id}", [
            'name' => 'Duilio',
            'email' => 'duilio@styde.net',
            'password' => '123456'
        ])->assertRedirect("/usuarios/{$user->id}");

        $this->assertCredentials([
            'name' => 'Duilio',
            'email' => 'duilio@styde.net',
            'password' => '123456',
        ]);
    }

    /** @test */
    function el_nombre_es_obligatorio_cuando_actualizas_el_usuario()
    {
        $user = factory(User::class)->create();

        $this->from("usuarios/{$user->id}/edit")
            ->put("usuarios/{$user->id}", [
                'name' => '',
                'email' => 'duilio@styde.net',
                'password' => '123456'
            ])
            ->assertRedirect("usuarios/{$user->id}/edit")
            ->assertSessionHasErrors(['name']);

        $this->assertDatabaseMissing('users', ['email' => 'duilio@styde.net']);
    }


    /** @test */
    function la_contrasena_es_opcional_cuando_actualizas_el_usuario()
    {
        $oldPassword = 'CLAVE_ANTERIOR';

        $user = factory(User::class)->create([
            'password' => bcrypt($oldPassword)
        ]);

        $this->from("usuarios/{$user->id}/edit")
            ->put("usuarios/{$user->id}", [
                'name' => 'Duilio',
                'email' => 'duilio@styde.net',
                'password' => ''
            ])
            ->assertRedirect("usuarios/{$user->id}"); // (users.show)

        $this->assertCredentials([
            'name' => 'Duilio',
            'email' => 'duilio@styde.net',
            'password' => $oldPassword // VERY IMPORTANT!
        ]);
    }

    /** @test */
    function el_email_tiene_ser_valido_para_el_usuario()
    {
        $user = factory(User::class)->create();

        $this->from("usuarios/{$user->id}/edit")
            ->put("usuarios/{$user->id}", [
                'name' => 'Duilio Palacios',
                'email' => 'correo-no-valido',
                'password' => '123456'
            ])
            ->assertRedirect("usuarios/{$user->id}/edit")
            ->assertSessionHasErrors(['email']);

        $this->assertDatabaseMissing('users', ['name' => 'Duilio Palacios']);
    }

    /** @test */
    function el_email_debe_ser_unico_cuando_actualizas_el_usuario()
    {
        //$this->withoutExceptionHandling();

        factory(User::class)->create([
            'email' => 'existing-email@example.com',
        ]);

        $user = factory(User::class)->create([
            'email' => 'duilio@styde.net'
        ]);

        $this->from("usuarios/{$user->id}/edit")
            ->put("usuarios/{$user->id}", [
                'name' => 'Duilio',
                'email' => 'existing-email@example.com',
                'password' => '123456'
            ])
            ->assertRedirect("usuarios/{$user->id}/edit")
            ->assertSessionHasErrors(['email']);

    }
    /** @test */
    function el_email_puede_permanecer_igual_al_actualizar_al_usuario()
    {
        $user = factory(User::class)->create([
            'email' => 'duilio@styde.net'
        ]);

        $this->from("usuarios/{$user->id}/edit")
            ->put("usuarios/{$user->id}", [
                'name' => 'Duilio Palacios',
                'email' => 'duilio@styde.net',
                'password' => '12345678'
            ])
            ->assertRedirect("usuarios/{$user->id}"); // (users.show)

        $this->assertDatabaseHas('users', [
            'name' => 'Duilio Palacios',
            'email' => 'duilio@styde.net',
        ]);
    }

    /** @test */
    public function eliminar_usuario()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $this->delete("usuarios/{$user->id}")
            ->assertRedirect('usuarios');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);

    }
}
