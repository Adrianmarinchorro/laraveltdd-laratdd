<?php

namespace Tests\Feature\Admin;

use App\Skill;
use App\User;
use App\UserProfile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteUsersTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    function it_send_a_user_to_the_trash()
    {
        $user = factory(User::class)->create();

        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();

        $user->skills()->attach([$skillA->id, $skillB->id]);

        $this->patch("usuarios/{$user->id}/papelera")
            ->assertRedirect(route('users.index'));

        // opcion 1
        $this->assertSoftDeleted('users', [
            'id' => $user->id,
        ]);

        $this->assertSoftDeleted('user_profiles', [
            'user_id' => $user->id,
        ]);


        // opcion 2

        // refresca el usuario
        $user->refresh();

        // para saber si el usuario fuen enviado a la papelera
        $this->assertTrue($user->trashed());

    }

    /** @test */
    function it_completely_deletes_a_user()
    {
        $user = factory(User::class)->create([
            'deleted_at' => now()
        ]);

        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();

        $user->skills()->attach([$skillA->id, $skillB->id]);

        $this->delete("usuarios/$user->id")
            ->assertRedirect(route('users.trashed'));

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function it_cannot_delete_a_user_that_is_not_in_the_trash()
    {
        $this->withExceptionHandling();

        $user = factory(User::class)->create([
            'deleted_at' => null
        ]);

        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();

        $user->skills()->attach([$skillA->id, $skillB->id]);

        $this->delete('usuarios/' . $user->id)
            ->assertStatus(404);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => null
        ]);
    }

}
