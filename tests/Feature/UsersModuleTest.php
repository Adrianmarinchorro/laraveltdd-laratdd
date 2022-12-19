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
            ->assertSee('Creando nuevo usuario');

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
            'password' => '123'
        ])->assertRedirect(route('users.index'));

        $this->assertCredentials([
            'name' => 'Adrián Marín',
            'email' => 'adri@gmail.com',
            'password' => '123'
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
            'password' => '123'
        ])
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['name' => 'El campo nombre es obligatorio']);

        $this->assertEquals(0, User::count());

//        $this->assertDatabaseMissing('users',[
//            'email' => 'adri@gmail.com'
//        ]);
    }

}