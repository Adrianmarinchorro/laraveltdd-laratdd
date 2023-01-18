<?php

namespace Tests\Feature\Admin;

use App\{Profession, Skill, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class CreateUsersTest extends TestCase
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
        'state' => 'active',
    ];

    /** @test */
    function it_loads_the_new_users_page()
    {

        $profession = factory(Profession::class)->create();

        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();

        $this->get('usuarios/nuevo')
            ->assertStatus(200)
            ->assertSee('Crear usuario')
            ->assertViewHas('professions', function ($professions) use ($profession) {
                return $professions->contains($profession);
            })
            ->assertViewHas('skills', function ($skills) use ($skillA, $skillB) {
                return $skills->contains($skillA) && $skills->contains($skillB);
            });

    }



    /** @test */
    function it_creates_a_new_user()
    {

        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();
        $skillC = factory(Skill::class)->create();

        $profession = factory(Profession::class)->create();

        $this->post('/usuarios/', $this->getValidData([
            'skills' => [$skillA->id, $skillB->id],
            'profession_id' => $profession->id,
        ]))->assertRedirect(route('users.index'));

        $this->assertCredentials([
            'first_name' => 'Adrián',
            'last_name' => 'Marín',
            'email' => 'adri@gmail.com',
            'password' => '1234567',
            'role' => 'user',
            'active' => true,
        ]);

        $user = User::findByEmail('adri@gmail.com');

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador de Laravel y Vue.js',
            'twitter' => 'https://twitter.com/el_charley',
            'profession_id' => $profession->id,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' =>$skillA->id,
        ]);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' =>$skillB->id,
        ]);

        $this->assertDatabaseMissing('user_skill', [
            'user_id' => $user->id,
            'skill_id' =>$skillC->id,
        ]);

    }

    /** @test */
    function the_user_is_redirected_to_the_previous_page_when_the_validation_fails()
    {
        $this->handleValidationExceptions();

        $this->from(route('users.create'))
            ->post('/usuarios/', [])
            ->assertRedirect(route('users.create'));

        $this->assertDatabaseEmpty('users');

    }

    /** @test */
    function the_first_name_is_required()
    {
        $this->handleValidationExceptions();


        $this->post('/usuarios/', $this->getValidData([
            'first_name' => '',
        ]))
            ->assertSessionHasErrors(['first_name' => 'El nombre es obligatorio']);

        $this->assertDatabaseEmpty('users');

    }

    /** @test */
    function the_last_name_is_required()
    {
        $this->handleValidationExceptions();


        $this->post('/usuarios/', $this->getValidData([
            'last_name' => '',
        ]))
            ->assertSessionHasErrors(['last_name' => 'Los apellidos son obligatorios']);

        $this->assertDatabaseEmpty('users');

    }

    /** @test */
    function the_email_is_required()
    {
        $this->handleValidationExceptions();



        $this->post('/usuarios/', $this->getValidData([
            'email' => '',
        ]))
            ->assertSessionHasErrors(['email' => 'El correo electrónico es obligatorio']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_must_be_valid()
    {
        $this->handleValidationExceptions();



        $this->post('/usuarios/', $this->getValidData([
            'email' => 'correonovalido',
        ]))
            ->assertSessionHasErrors(['email' => 'El correo electrónico debe ser válido']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_must_be_unique()
    {
        $this->handleValidationExceptions();

        factory(User::class)->create([
            'email' => 'adri@gmail.com'
        ]);


        $this->post('/usuarios/', $this->getValidData([
            'email' => 'adri@gmail.com',
        ]))
            ->assertSessionHasErrors(['email' => 'El correo electrónico debe ser único']);

        $this->assertDatabaseCount('users', 1);

    }

    /** @test */
    function the_password_is_required()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', $this->getValidData(['password' => '']))
            ->assertSessionHasErrors(['password' => 'La contraseña es obligatoria']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_role_is_optional()
    {
        $this->post('/usuarios/', $this->getValidData([
            'role' => null,
        ]));


        $this->assertDatabaseHas('users',[
            'email' => 'adri@gmail.com',
            'role' => 'user'
        ]);

    }

    /** @test */
    function the_role_must_be_valid()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', $this->getValidData([
            'role' => 'invalid-role',
        ]))
            ->assertSessionHasErrors(['role']);

        $this->assertDatabaseEmpty('users');

    }

    /** @test */
    function the_profession_is_optional()
    {
        $this->post('/usuarios/', $this->getValidData([
            'profession_id' => '',
        ]));

        $this->assertCredentials([
            'first_name' => 'Adrián',
            'last_name' => 'Marín',
            'email' => 'adri@gmail.com',
            'password' => '1234567',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => User::findByEmail('adri@gmail.com')->id,
            'bio' => 'Programador de Laravel y Vue.js',
            'twitter' => 'https://twitter.com/el_charley',
            'profession_id' => null,
        ]);

    }

    /** @test */
    function the_profession_must_be_valid()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', $this->getValidData([
            'profession_id' => '999',
        ]))
            ->assertSessionHasErrors(['profession_id' => 'La profesión debe ser válida']);


        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function only_selectable_professions_are_valid()
    {
        $this->handleValidationExceptions();

        $invalidProfession = factory(Profession::class)->create([
            'selectable' => false
        ]);

        $this->post('/usuarios/', $this->getValidData([
            'profession_id' => $invalidProfession->id,
        ]))
            ->assertSessionHasErrors(['profession_id' => 'La profesión debe ser válida']);


        $this->assertDatabaseEmpty('users');

    }

    /** @test */
    function only_not_deleted_professions_are_valid()
    {
        $this->handleValidationExceptions();

        $deletedProfession = factory(Profession::class)->create([
            'deleted_at' => now()->format('Y-m-d'),
        ]);


        $this->post('/usuarios/', $this->getValidData([
            'profession_id' => $deletedProfession->id,
        ]))
            ->assertSessionHasErrors(['profession_id' => 'La profesión debe ser válida']);


        $this->assertDatabaseEmpty('users');

    }

    /** @test */
    function the_password_must_be_more_than_six_characters()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', [
            'name' => 'Adrian Marín',
            'email' => 'adri@gmail.com',
            'password' => '1A3a56'
        ])
            ->assertSessionHasErrors(['password' => 'La contraseña debe tener mas de seis caracteres']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_bio_is_required()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', [
            'name' => 'Adrian Marín',
            'email' => 'adrian@gmail.com',
            'password' => '1234567',
            'bio' => '',
            'twitter' => 'https://twitter.com/el_charley',
        ])
            ->assertSessionHasErrors(['bio' => 'La biografia es obligatoria']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_twitter_must_be_an_url()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', [
            'name' => 'Adrian Marín',
            'email' => 'adrian@gmail.com',
            'password' => '1234567',
            'bio' => 'Programador back-end',
            'twitter' => 'no-soy-una-url',
        ])
            ->assertSessionHasErrors(['twitter' => 'El twitter debe ser una url']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_twitter_is_optional()
    {
        $this->post('/usuarios/', $this->getValidData([
            'twitter' => null,
        ]));

        $this->assertCredentials([
            'first_name' => 'Adrián',
            'last_name' => 'Marín',
            'email' => 'adri@gmail.com',
            'password' => '1234567',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => User::findByEmail('adri@gmail.com')->id,
            'bio' => 'Programador de Laravel y Vue.js',
            'twitter' => null,
        ]);

    }

    /** @test */
    function the_skills_must_be_an_array()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', $this->getValidData([
            'skills' => 'no un array',
        ]))
            ->assertSessionHasErrors(['skills']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_skills_must_be_valid()
    {
        $this->handleValidationExceptions();

        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();

        $this->post('/usuarios/', $this->getValidData([
            'skills' => [$skillA->id, $skillB->id + 1],
        ]))
            ->assertSessionHasErrors(['skills']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_state_must_be_valid()
    {
        $this->withExceptionHandling();

        $this->from('usuarios/nuevo')
            ->post('usuarios', $this->getValidData([
                'state' => 'invalid-state',
            ]))
            ->assertSessionHasErrors('state');

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_state_is_required()
    {
        $this->withExceptionHandling();

        $this->from('usuarios/nuevo')
            ->post('usuarios', $this->getValidData([
                'state' => null,
            ]))
            ->assertSessionHasErrors('state');

        $this->assertDatabaseEmpty('users');
    }

}
