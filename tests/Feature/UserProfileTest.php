<?php

namespace Tests\Feature;

use App\Profession;
use App\User;
use App\UserProfile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    protected $defaultData = [
        'name' => 'Adrián Marín',
        'email' => 'adri@gmail.com',
        'profession_id' => '',
        'bio' => 'Programador de Laravel y Vue.js',
        'twitter' => 'https://twitter.com/el_charley',
        'role' => 'user'
    ];

    /** @test */
    function a_user_can_edit_its_profile()
    {
        $user = factory(User::class)->create();
        $user->profile()->save(factory(UserProfile::class)->make());

        $newProfession = factory(Profession::class)->create();

        // $this->actingAs($user); sirve para conectarme como este usuario

        $response = $this->get('/editar-perfil/');

        $response->assertStatus(200);

        $response = $this->put('/editar-perfil', [
            'name' => 'Adrián Marín',
            'email' => 'adri@gmail.com',
            'profession_id' => $newProfession->id,
            'bio' => 'Programador de Laravel y Vue.js',
            'twitter' => 'https://twitter.com/el_charley',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'name' => 'Adrián Marín',
            'email' => 'adri@gmail.com',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'profession_id' => $newProfession->id,
            'bio' => 'Programador de Laravel y Vue.js',
            'twitter' => 'https://twitter.com/el_charley',
        ]);

    }

    /** @test */
    function the_user_cannot_change_its_role()
    {
        $user = factory(User::class)->create([
            'role' => 'user'
        ]);

        $response = $this->put('/editar-perfil/', $this->getValidData([
            'role' => 'admin'
        ]));

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
           'id' => $user->id,
           'role' => 'user',
        ]);

    }

    /** @test */
    function the_user_cannot_change_its_password()
    {

        $user = factory(User::class)->create([
            'password' => bcrypt('1111111')
        ]);

        $response = $this->put('/editar-perfil/', $this->getValidData([
            'password' => '1111112'
        ]));

        $response->assertRedirect();

        $this->assertCredentials([
            'id' => $user->id,
            'password' => '1111111',
        ]);

    }

}