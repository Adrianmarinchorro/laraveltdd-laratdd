<?php

namespace Tests\Feature\Admin;

use App\Profession;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateUsersTest extends TestCase
{

    use RefreshDatabase;

    protected $defaultData = [
        'name' => 'Adrián Marín',
        'email' => 'adri@gmail.com',
        'password' => '1234567',
        'profession_id' => '',
        'bio' => 'Programador de Laravel y Vue.js',
        'twitter' => 'https://twitter.com/el_charley',
        'role' => 'user'
    ];

    /** @test */
    function it_loads_the_edit_user_page()
    {
        $user = factory(User::class)->create();


        //$this->get('usuarios/editar/', ['id' => $user->id]) uri: usuarios/editar?id=5
        $this->get("usuarios/{$user->id}/editar") // uri: usuarios/5/editar
        ->assertStatus(200)
            ->assertViewIs('users.edit')
            ->assertSee('Editar usuario')
            ->assertViewHas('user', function ($viewUser) use ($user) {
                return $viewUser->id === $user->id;
            });
    }

    /** @test */
    function it_updates_a_user()
    {
        $user = factory(User::class)->create();


        $this->put("/usuarios/{$user->id}", [
            'name' => 'Adrián Marín',
            'email' => 'adri@gmail.com',
            'password' => '1234567'
        ])->assertRedirect(route('users.show', ['user' => $user]));

        $this->assertCredentials([
            'name' => 'Adrián Marín',
            'email' => 'adri@gmail.com',
            'password' => '1234567'
        ]);
    }

    /** @test */
    function the_name_is_required()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();


        $this->from("usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", [
                'name' => '',
                'email' => 'adri@gmail.com',
                'password' => '1234567'
            ])->assertRedirect(route('users.edit', ['user' => $user]))
            ->assertSessionHasErrors(['name' => 'El nombre es obligatorio']);

        $this->assertDatabaseMissing('users', ['email' => 'adri@gmail.com']);
    }

    /** @test */
    function the_email_is_required()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();

        $this->from(route('users.edit', ['user' => $user]));

        $this->put("/usuarios/{$user->id}", [
            'name' => 'Adrian Marín',
            'email' => '',
            'password' => '1234567'
        ])
            ->assertRedirect(route('users.edit', ['user' => $user]))
            ->assertSessionHasErrors(['email' => 'El correo electrónico es obligatorio']);

        $this->assertDatabaseMissing('users', ['name' => 'Adrian Marín']);
    }


    /** @test */
    function the_email_must_be_valid()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();

        $this->from(route('users.edit', ['user' => $user]));

        $this->put("/usuarios/{$user->id}", [
            'name' => 'Adrian Marín',
            'email' => 'correo-no-valido',
            'password' => '1234567'
        ])
            ->assertRedirect(route('users.edit', ['user' => $user]))
            ->assertSessionHasErrors(['email' => 'El correo electrónico debe ser valido']);


        $this->assertDatabaseMissing('users', ['name' => 'Adrian Marín']);
    }

    /** @test */
    function the_email_must_be_unique()
    {
        $this->handleValidationExceptions();

        factory(User::class)->create([
            'email' => 'existingmail@example.com'
        ]);

        $user = factory(User::class)->create([
            'email' => 'adrian@gmail.com'
        ]);


        $this->from(route('users.update', ['user' => $user]));

        $this->put("/usuarios/{$user->id}", [
            'name' => 'Adrian Marín',
            'email' => 'existingmail@example.com',
            'password' => '1234567'
        ])
            ->assertRedirect(route('users.update', ['user' => $user]))
            ->assertSessionHasErrors(['email']);


    }

    /** @test */
    function the_email_can_stay_the_same()
    {
        $user = factory(User::class)->create([
            'email' => 'adri@gmail.com',
        ]);

        $this->from(route('users.edit', ['user' => $user]))
            ->put("/usuarios/{$user->id}", [
                'name' => 'Adrian Marín',
                'email' => 'adri@gmail.com',
                'password' => '12345678'
            ])
            ->assertRedirect(route('users.show', ['user' => $user]));

        $this->assertDatabaseHas('users', [
            'name' => 'Adrian Marín',
            'email' => 'adri@gmail.com',
        ]);
    }

    /** @test */
    function the_password_is_optional()
    {
        $this->handleValidationExceptions();
        //$this->withExceptionHandling(); ( este y el de arriba ¿son similares?)

        $old_password = 'clave_anterior';

        $user = factory(User::class)->create([
            'password' => bcrypt($old_password)
        ]);

        $this->from(route('users.edit', ['user' => $user]))
            ->put("/usuarios/{$user->id}", [
                'name' => 'Adrian Marín',
                'email' => 'adri@gmail.com',
                'password' => ''
            ])
            ->assertRedirect(route('users.show', ['user' => $user]));

        $this->assertCredentials([
            'name' => 'Adrian Marín',
            'email' => 'adri@gmail.com',
            'password' => $old_password,
        ]);
    }

}
