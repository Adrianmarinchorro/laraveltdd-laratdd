<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersModuleTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    function it_loads_the_users_list_page()
    {
        $this->withoutExceptionHandling();

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
    function it_shows_a_default_message_if_the_users_list_is_empty()
    {


        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('Listado de usuarios')
            ->assertSee('No hay usuarios registrados.');
    }

    /** @test */
    function it_displays_the_users_details()
    {

        $user = factory(User::class)->create([
                'name' => 'Adrián Marín',
        ]);

        $this->get('/usuarios/' . $user->id)
            ->assertStatus(200)
            ->assertSee($user->name);
    }

    /** @test */
    function it_loads_the_new_users_page()
    {
        $this->get('usuarios/nuevo')
            ->assertStatus(200)
            ->assertSee('Crear usuario');

    }

    /** @test */
    function it_display_a_404_error_if_the_user_is_not_found()
    {

        $this->get('/usuarios/999')
            ->assertStatus(404)
            ->assertSee('Página no encontrada');

    }

    /** @test */
    function it_creates_a_new_user()
    {
        $this->withoutExceptionHandling();

        $this->post('/usuarios/', [
            'name' => 'Adrián Marín',
            'email' => 'adri@gmail.com',
            'password' => '1234567'
        ])->assertRedirect(route('users.index'));

        $this->assertCredentials([
            'name' => 'Adrián Marín',
            'email' => 'adri@gmail.com',
            'password' => '1234567'
            ]);
    }

    /** @test */
    function the_name_is_required()
    {
        //$this->withoutExceptionHandling();

        $this->from(route('users.create'));

        $this->post('/usuarios/', [
            'name' => '',
            'email' => 'adri@gmail.com',
            'password' => '1234567'
        ])
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['name' => 'El nombre es obligatorio']);

        $this->assertEquals(0, User::count());

//        $this->assertDatabaseMissing('users',[
//            'email' => 'adri@gmail.com'
//        ]);
    }

    /** @test */
    function the_email_is_required()
    {
        // $this->withoutExceptionHandling();

        $this->from(route('users.create'));

        $this->post('/usuarios/', [
            'name' => 'Adrian Marín',
            'email' => '',
            'password' => '1234567'
        ])
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['email' => 'El correo electrónico es obligatorio']);

        $this->assertEquals(0, User::count());
    }

    /** @test */
    function the_email_must_be_valid()
    {
        // $this->withoutExceptionHandling();

        $this->from(route('users.create'));

        $this->post('/usuarios/', [
            'name' => 'Adrian Marín',
            'email' => 'correonovalido',
            'password' => '1234567'
        ])
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['email' => 'El correo electrónico debe ser válido']);

        $this->assertEquals(0, User::count());
    }

    /** @test */
    function the_email_must_be_unique()
    {
        // $this->withoutExceptionHandling();

        factory(User::class)->create([
                'email' => 'adrian@gmail.com'
            ]);

        $this->from(route('users.create'));

        $this->post('/usuarios/', [
            'name' => 'Adrian Marín',
            'email' => 'adrian@gmail.com',
            'password' => '1234567'
        ])
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['email' => 'El correo electrónico debe ser único']);

        $this->assertEquals(1, User::count());
    }

    /** @test */
    function the_password_is_required()
    {
        //$this->withoutExceptionHandling();

        $this->from(route('users.create'));

        $this->post('/usuarios/', [
            'name' => 'Adrian Marín',
            'email' => 'adri@gmail.com',
            'password' => ''
        ])
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['password' => 'La contraseña es obligatoria']);

        $this->assertEquals(0, User::count());
    }

    /** @test */
    function the_password_must_be_more_than_six_characters()
    {
        //$this->withoutExceptionHandling();

        $this->from(route('users.create'));

        $this->post('/usuarios/', [
            'name' => 'Adrian Marín',
            'email' => 'adri@gmail.com',
            'password' => '1A3a56'
        ])
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['password' => 'La contraseña debe tener mas de seis caracteres']);

        $this->assertEquals(0, User::count());
    }

}