<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PracticandoTest extends TestCase
{
	use RefreshDatabase;
    /** @test */
    function mostrando_usuarios()
    {
    	factory(User::class)->create([
    		'name' => 'Joel',
    	]);

    	factory(User::class)->create([
    		'name' => 'Ellie',
    	]);

    	$this->get('/usuarios')
    		->assertStatus(200)
    		->assertSee('Listado de usuarios')
    		->assertSee('Joel')
    		->assertSee('Ellie');
    }

    /** @test */
    function mostrar_mensaje_de_la_lista_vacia_de_usuarios()
    {
    	$this->get('/usuarios')
    		->assertStatus(200)
    		->assertSee('No hay usuarios registrados');
    }

    /** @test */
    function mostrar_error_404_usuario_no_encontrado()
    {
    	$this->get('/usuarios/9999')
    		->assertStatus(404)
    		->assertSee('Página no encontrada');
    }
    /** @test */
    function mostrar_formulario_nuevo_usuario()
    {
    	$this->get('/usuarios/nuevo')
    		->assertStatus(200)
    		->assertSee('Nuevo Usuario');
    }

    /** @test */
    function almacena_el_nuevo_usuario()
    {
    	$this->post('/usuarios/',[
    		'name' => 'Alexander Ruiz',
    		'email' => 'alexander@areg.net',
    		'password' => '123456'
    	]);
    	//comprobar que se ha creado un usuario con los datos que hemos pasado en la petición POST
    	//$this->assertDatabaseHas('table',['attributes'])

    	$this->assertCredentials([
    		'name' => 'Alexander Ruiz',
    		'email' => 'alexander@areg.net',
    		'password' => '123456'
    	]);
    }

    /** test */
    function el_name_es_obligatorio()
    {
    	$this->from('usuarios/create')
    		->post('/usuarios/',[
    		'name' => '',
    		'email' => 'alexander@areg.net',
    		'password' => '123456'
    	])
    	->assertRedirect('/usuarios/create')
    	->assertSessionHasErrors(['name' => 'El campo nombre es obligatorio']);

    	$this->assertEquals(0, User::count());
    }

    /** @test */
    function el_email_es_obligatorio()
    {
    	$this->from('usuarios/create')
    		->post('/usuarios/',[
    		'name' => 'Alexander Ruiz',
    		'email' => '',
    		'password' => '123456'
    	])
    	->assertRedirect('/usuarios/create')
    	->assertSessionHasErrors(['email']);

    	$this->assertEquals(0, User::count());
    }

    /** @test */
    function email_valido()
    {
    	$this->from('usuarios/create')
    		->post('/usuarios/',[
    			'name' => 'Alexander Ruiz',
    			'email' => 'correo-no-valido',
    			'password' => '123456'
    		])
    		->assertRedirect('/usuarios/create')
    		->assertSessionHasErrors(['email']);

    	$this->assertEquals(0, User::count());
    }

    /** @test */
    function el_email_tiene_que_ser_unico()
    {
    	factory(User::class)->create([
    		'email' => 'alexander@areg.com'
    	]);
    	$this->from('/usuarios/create')
    		->post('/usuarios/',[
    			'name' => 'Alexander Ruiz',
    			'email' => 'alexander@areg.net',
    			'password' => '123456'
    		])
    	->assertRedirect('/usuarios/create')
    	->assertSessionHasErrors(['email']);

    	$this->assertEquals(1, User::count());
    }

    /** @test */
    function el_password_es_obligatorio()
    {
    	$this->from('/usuarios/create')
    		->post('/usuarios/',[
    			'name' => 'Alexander Ruiz',
    			'email' => 'alexander@areg.net',
    			'password' => ''
    		])
    		->assertRedirect('/usuarios/create')
    		->assertSessionHasErrors(['password']);

    	$this->assertEquals(0, User::count());
    }

    //Actualizando Usuarios
    /** @test */
    function mostrar_formulario_editar_usuario()
    {
    	$user = factory(User::class)->create();
    	$this->get("/usuarios/{$user->id}/edit")
    		->assertStatus(200)
    		->assertViewIs('user.edit')
    		->assertSee('Editar Usuario')
    		->assertViewHas('user',function ($viewUser) use ($user) {
    			return $viewUser->id === $user->id;
    		});
    }

    /** @test */
    function almacena_el_usuario_actualizado()
    {
    	$user = factory(User::class)->create();
    	$this->put("/usuarios/{$user->id}",[
    		'name' => 'Alexander Ruiz',
    		'email' => 'alexander@areg.net',
    		'password' => '123456'
    	])->assertRedirect("/usuarios/{$user->id}");
    	$this->assertCredentials([
    		'name' => 'Alexander Ruiz',
    		'email' => 'alexander@areg.net',
    		'password' => '123456'
    	]);
    }

    /** @test */
    function el_name_es_obligatorio_para_actualizar_usuario()
    {
    	$user = factory(User::class)->create();
    	$this->from("usuarios/{$user->id}/edit")
    		->put("/usuarios/{$user->id}", [
    		'name' => '',
    		'email' => 'alexander@areg.net',
    		'password' => '123456'
    	])
    	->assertRedirect("/usuarios/{$user->id}/edit")
    	->assertSessionHasErrors(['name']);
    	// También puede usar la ayuda assertDatabaseMissing para afirmar que los datos no existen en la base de datos.
    	$this->assertDatabaseMissing('users', ['email' => 'alexander@areg.net']);
    }

    /** @test */
    function el_email_es_obligatorio_y_valido_para_actualizar_usuario()
    {
        $user = factory(User::class)->create();

        $this->from("usuarios/{$user->id}/edit")
            ->put("usuarios/{$user->id}", [
                'name' => 'Alexander Ruiz',
                'email' => 'correo-no-valido',
                'password' => '123456'
            ])
            ->assertRedirect("usuarios/{$user->id}/edit")
            ->assertSessionHasErrors(['email']);

        $this->assertDatabaseMissing('users', ['name' => 'Alexander Ruiz']);
    }

    /** @test */
    function el_email_tiene_que_ser_unico_y_valido_para_actualizar_usuario()
    {
        factory(User::class)->create([
            'email' => 'existing-email@example.com',
        ]);

        $user = factory(User::class)->create([
            'email' => 'alexander@areg.net'
        ]);

        $this->from("usuarios/{$user->id}/edit")
            ->put("usuarios/{$user->id}", [
                'name' => 'Alexander Ruiz',
                'email' => 'existing-email@example.com',
                'password' => '123456'
            ])
            ->assertRedirect("usuarios/{$user->id}/edit")
            ->assertSessionHasErrors(['email']);
    }

    /** @test */
    function el_users_email_puede_permanecer_igual_al_actualizar_usuario()
    {
        $user = factory(User::class)->create([
            'email' => 'alexander@areg.net'
        ]);

        $this->from("usuarios/{$user->id}/edit")
            ->put("usuarios/{$user->id}", [
                'name' => 'Alexander Ruiz',
                'email' => 'alexander@areg.net',
                'password' => '12345678'
            ])
            ->assertRedirect("usuarios/{$user->id}"); // (users.show)

        $this->assertDatabaseHas('users', [
            'name' => 'Alexander Ruiz',
            'email' => 'alexander@areg.net',
        ]);
    }

    /** @test */
    function el_password_es_opcional_para_actualizar_usuario()
    {
        $oldPassword = 'CLAVE_ANTERIOR';

        $user = factory(User::class)->create([
            'password' => bcrypt($oldPassword)
        ]);

        $this->from("usuarios/{$user->id}/edit")
            ->put("usuarios/{$user->id}", [
                'name' => 'Alexander Ruiz',
                'email' => 'alexander@areg.net',
                'password' => ''
            ])
            ->assertRedirect("usuarios/{$user->id}"); // (users.show)

        $this->assertCredentials([
            'name' => 'Alexander Ruiz',
            'email' => 'alexander@areg.net',
            'password' => $oldPassword // VERY IMPORTANT!
        ]);
    }

    /** @test */
    function it_deletes_a_user()
    {
        //$this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this->delete("/usuarios/{$user->id}")
            ->assertRedirect('/usuarios');

        $this->assertDatabaseMissing('users', [
           'id' => $user->id
        ]);
    }


}
