<?php

namespace Tests\Feature;

use App\Profession;
use App\Skill;
use App\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersModuleTest extends TestCase
{

    use RefreshDatabase;

    protected $profession;

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

        $this->withoutExceptionHandling();

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

        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();
        $skillC = factory(Skill::class)->create();

        $this->post('/usuarios/', $this->getValidData([
            'skills' => [$skillA->id, $skillB->id],
        ]))->assertRedirect(route('users.index'));

        $this->assertCredentials([
            'name' => 'Adrián Marín',
            'email' => 'adri@gmail.com',
            'password' => '1234567',
        ]);

        $user = User::findByEmail('adri@gmail.com');

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador de Laravel y Vue.js',
            'twitter' => 'https://twitter.com/el_charley',
            'profession_id' => $this->profession->id,
            'user_id' => $user->id, // o User::first()->id
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
    function the_name_is_required()
    {
        //$this->withoutExceptionHandling();

        $this->from(route('users.create'));

        $this->post('/usuarios/', $this->getValidData([
            'name' => '',
        ]))
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['name' => 'El nombre es obligatorio']);

        $this->assertDatabaseEmpty('users');

    }

    /** @test */
    function the_email_is_required()
    {
        // $this->withoutExceptionHandling();

        $this->from(route('users.create'));

        $this->post('/usuarios/', $this->getValidData([
            'email' => '',
        ]))
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['email' => 'El correo electrónico es obligatorio']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_must_be_valid()
    {
        // $this->withoutExceptionHandling();

        $this->from(route('users.create'));

        $this->post('/usuarios/', $this->getValidData([
            'email' => 'correonovalido',
        ]))
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['email' => 'El correo electrónico debe ser válido']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_must_be_unique()
    {
        // $this->withoutExceptionHandling();

        factory(User::class)->create([
            'email' => 'adri@gmail.com'
        ]);

        $this->from(route('users.create'));

        $this->post('/usuarios/', $this->getValidData([
            'email' => 'adri@gmail.com',
        ]))
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['email' => 'El correo electrónico debe ser único']);

        $this->assertDatabaseCount('users', 1);
        //$this->assertEquals(1, User::count());
    }

    /** @test */
    function the_password_is_required()
    {
        //$this->withoutExceptionHandling();

        $this->from(route('users.create'));

        $this->post('/usuarios/', $this->getValidData(['password' => '']))
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['password' => 'La contraseña es obligatoria']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_profession_is_optional()
    {
        //$this->withoutExceptionHandling();

        $this->from(route('users.create'));

        $this->post('/usuarios/', $this->getValidData([
            'profession_id' => '',
        ]))
            ->assertRedirect(route('users.index'));

        $this->assertCredentials([
            'name' => 'Adrián Marín',
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
        // $this->witExceptionHandling();
        // ó
        // $this->handleValidationExceptions();

        $this->from(route('users.create'));

        $this->post('/usuarios/', $this->getValidData([
            'profession_id' => '999',
        ]))
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['profession_id' => 'La profesión debe ser válida']);


        $this->assertDatabaseEmpty('users');

    }

    /** @test */
    function only_selectable_professions_are_valid()
    {

        $invalidProfession = factory(Profession::class)->create([
            'selectable' => false
        ]);

        $this->from(route('users.create'));

        $this->post('/usuarios/', $this->getValidData([
            'profession_id' => $invalidProfession->id,
        ]))
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['profession_id' => 'La profesión debe ser válida']);


        $this->assertDatabaseEmpty('users');

    }

    /** @test */
    function only_not_deleted_professions_are_valid()
    {

        $deletedProfession = factory(Profession::class)->create([
            'deleted_at' => now()->format('Y-m-d'),
        ]);

        $this->from(route('users.create'));

        $this->post('/usuarios/', $this->getValidData([
            'profession_id' => $deletedProfession->id,
        ]))
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['profession_id' => 'La profesión debe ser válida']);


        $this->assertDatabaseEmpty('users');

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

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_bio_is_required()
    {
        // $this->withoutExceptionHandling();

        $this->from(route('users.create'));

        $this->post('/usuarios/', [
            'name' => 'Adrian Marín',
            'email' => 'adrian@gmail.com',
            'password' => '1234567',
            'bio' => '',
            'twitter' => 'https://twitter.com/el_charley',
        ])
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['bio' => 'La biografia es obligatoria']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_twitter_must_be_an_url()
    {
        // $this->withoutExceptionHandling();

        $this->from(route('users.create'));

        $this->post('/usuarios/', [
            'name' => 'Adrian Marín',
            'email' => 'adrian@gmail.com',
            'password' => '1234567',
            'bio' => 'Programador back-end',
            'twitter' => 'no-soy-una-url',
        ])
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['twitter' => 'El twitter debe ser una url']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_twitter_is_optional()
    {
        $this->withoutExceptionHandling();

        $this->from(route('users.create'));

        $this->post('/usuarios/', $this->getValidData([
            'twitter' => null,
        ]))
            ->assertRedirect(route('users.index'));

        $this->assertCredentials([
            'name' => 'Adrián Marín',
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
        //$this->withoutExceptionHandling();

        $this->from(route('users.create'));

        $this->post('/usuarios/', $this->getValidData([
            'skills' => 'no un array',
        ]))
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['skills']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_skills_must_be_valid()
    {
        //$this->withoutExceptionHandling();

        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();

        $this->from(route('users.create'));

        $this->post('/usuarios/', $this->getValidData([
            'skills' => [$skillA->id, $skillB->id + 1],
        ]))
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['skills']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function it_loads_the_edit_user_page()
    {
        // $this->withoutExceptionHandling();

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
        //$this->withoutExceptionHandling();

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
    function the_name_is_required_when_updating_a_user()
    {
        //$this->withoutExceptionHandling();

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
    function the_email_is_required_when_updating_a_user()
    {
        // $this->withoutExceptionHandling();

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
    function the_email_must_be_valid_when_updating_a_user()
    {
        //$this->withoutExceptionHandling();

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
    function the_email_must_be_unique_when_updating_a_user()
    {
        // $this->withoutExceptionHandling();

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
    function the_email_can_stay_the_same_when_updating_the_user()
    {
        $this->withoutExceptionHandling();

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
    function the_password_is_optional_when_updating_the_user()
    {
        //$this->withoutExceptionHandling();

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

    /** @test */
    function it_deletes_a_user()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this->delete("usuarios/$user->id")
            ->assertRedirect(route('users.index'));


        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        // $this->assertSame(0, User::count());
    }

    /**
     * @return string[]
     */
    public function getValidData(array $custom = []): array
    {

        $this->profession = factory(Profession::class)->create();

        // combina ambos array priorizando el array con atributos personalizados
        return array_filter(array_merge([
            'name' => 'Adrián Marín',
            'email' => 'adri@gmail.com',
            'password' => '1234567',
            'profession_id' => $this->profession->id,
            'bio' => 'Programador de Laravel y Vue.js',
            'twitter' => 'https://twitter.com/el_charley'
        ], $custom));
    }

}