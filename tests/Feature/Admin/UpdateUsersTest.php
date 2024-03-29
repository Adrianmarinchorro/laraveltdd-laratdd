<?php

namespace Tests\Feature\Admin;

use App\Profession;
use App\Skill;
use App\User;
use App\UserProfile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateUsersTest extends TestCase
{

    use RefreshDatabase;

    protected $defaultData = [
        'first_name' => 'Adrián',
        'last_name' => 'Marín',
        'email' => 'adri@gmail.com',
        'password' => '1234567',
        'profession_id' => '',
        'bio' => 'Programador de Laravel y Vue.js',
        'twitter' => 'https://twitter.com/el_charley',
        'role' => 'user',
        'state' => 'inactive',
    ];

    /** @test */
    function it_loads_the_edit_user_page()
    {
        $user = factory(User::class)->create();

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

        $oldProfession = factory(Profession::class)->create();

        $user->profile()->update([
            'profession_id' => $oldProfession->id,
        ]);

        $oldSkill1 = factory(Skill::class)->create();
        $oldSkill2 = factory(Skill::class)->create();

        $user->skills()->attach([$oldSkill1->id, $oldSkill2->id]);

        $newProfession = factory(Profession::class)->create();
        $newSkill1 = factory(Skill::class)->create();
        $newSkill2 = factory(Skill::class)->create();

        // cambiamos solo las que son diferentes de las por defecto
        $this->put("/usuarios/{$user->id}", $this->getValidData([
            'role' => 'admin',
            'profession_id' => $newProfession->id,
            'skills' => [$newSkill1->id, $newSkill2->id],
            'state' => 'inactive' // inactive ya que al crearlo se hace activo y queremos verlo ahora inactive
        ]))->assertRedirect(route('users.show', ['user' => $user]));

        $this->assertCredentials([
            'first_name' => 'Adrián',
            'last_name' => 'Marín',
            'email' => 'adri@gmail.com',
            'password' => '1234567',
            'role' => 'admin',
            'active' => false,
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'bio' => 'Programador de Laravel y Vue.js',
            'twitter' => 'https://twitter.com/el_charley',
            'profession_id' => $newProfession->id,
        ]);

        $this->assertDatabaseCount('user_skill', 2);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $newSkill1->id
        ]);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $newSkill2->id
        ]);

    }

    /** @test */
    function the_first_name_is_required()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();


        $this->from("usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", $this->getValidData([
                'first_name' => '',
            ]))->assertRedirect(route('users.edit', ['user' => $user]))
            ->assertSessionHasErrors(['first_name' => 'El nombre es obligatorio']);

        $this->assertDatabaseMissing('users', ['email' => 'adri@gmail.com']);
    }

    /** @test */
    function the_last_name_is_required()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();


        $this->from("usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", $this->getValidData([
                'last_name' => '',
            ]))->assertRedirect(route('users.edit', ['user' => $user]))
            ->assertSessionHasErrors(['last_name' => 'Los apellidos son obligatorios']);

        $this->assertDatabaseMissing('users', ['email' => 'adri@gmail.com']);
    }

    /** @test */
    function the_email_is_required()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();

        $this->from(route('users.edit', ['user' => $user]));

        $this->put("/usuarios/{$user->id}", $this->getValidData([
            'email' => '',
        ]))
            ->assertRedirect(route('users.edit', ['user' => $user]))
            ->assertSessionHasErrors(['email' => 'El correo electrónico es obligatorio']);

        $this->assertDatabaseMissing('users', ['first_name' => 'Adrian', 'last_name' => 'Marín']);
    }


    /** @test */
    function the_email_must_be_valid()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();

        $this->from(route('users.edit', ['user' => $user]));

        $this->put("/usuarios/{$user->id}", $this->getValidData([
            'email' => 'correo-no-valido',
        ]))
            ->assertRedirect(route('users.edit', ['user' => $user]))
            ->assertSessionHasErrors(['email' => 'El correo electrónico debe ser valido']);


        $this->assertDatabaseMissing('users', ['first_name' => 'Adrian', 'last_name' => 'Marín']);
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

        $this->put("/usuarios/{$user->id}", $this->getValidData([
            'email' => 'existingmail@example.com',
            ]))
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
            ->put("/usuarios/{$user->id}", $this->getValidData([
                'email' => 'adri@gmail.com',
            ]))
            ->assertRedirect(route('users.show', ['user' => $user]));

        $this->assertDatabaseHas('users', [
            'first_name' => 'Adrian',
            'last_name' => 'Marín',
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
            ->put("/usuarios/{$user->id}", $this->getValidData([
                'password' => ''
            ]))
            ->assertRedirect(route('users.show', ['user' => $user]));

        $this->assertCredentials([
            'first_name' => 'Adrian',
            'last_name' => 'Marín',
            'email' => 'adri@gmail.com',
            'password' => $old_password,
        ]);
    }

    /** @test */
    function it_detaches_all_the_skills_if_none_is_checked()
    {
        $user = factory(User::class)->create();

        $oldProfession = factory(Profession::class)->create();

        $user->profile()->update([
            'profession_id' => $oldProfession->id,
        ]);

        $oldSkill1 = factory(Skill::class)->create();
        $oldSkill2 = factory(Skill::class)->create();

        $user->skills()->attach([$oldSkill1->id, $oldSkill2->id]);

        $this->put("/usuarios/{$user->id}", $this->getValidData([]))
            ->assertRedirect(route('users.show', ['user' => $user]));


        $this->assertDatabaseEmpty('user_skill');

    }

    /** @test */
    function the_role_is_required()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();

        $this->from("usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}", $this->getValidData([
                'role' => '',
            ]))->assertRedirect(route('users.edit', ['user' => $user]))
            ->assertSessionHasErrors(['role']);

        $this->assertDatabaseMissing('users', ['email' => 'adri@gmail.com']);
    }

    /** @test */
    function the_state_is_required()
    {
        $this->withExceptionHandling();

        $user = factory(User::class)->create();

        $this->from('usuarios/' . $user->id . '/editar')
            ->put("usuarios/{$user->id}", $this->getValidData([
                'state' => '',
            ]))
            ->assertRedirect('usuarios/' . $user->id . '/editar')
            ->assertSessionHasErrors('state');

        $this->assertDatabaseMissing('users', ['first_name' => 'Adrián']);
    }

    /** @test */
    function the_state_must_be_valid()
    {
        $this->withExceptionHandling();

        $user = factory(User::class)->create();

        $this->from('usuarios/' . $user->id . '/editar')
            ->put("usuarios/{$user->id}", $this->getValidData([
                'state' => 'invalid-state',
            ]))
            ->assertRedirect('usuarios/' . $user->id . '/editar')
            ->assertSessionHasErrors('state');

        $this->assertDatabaseMissing('users', ['first_name' => 'Adrián']);
    }

}
