<?php

namespace Tests\Feature\Admin;

use App\Skill;
use App\User;
use App\UserProfile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RestoreUsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_restore_a_trashed_user()
    {
        $user = factory(User::class)->create([
            'deleted_at' => now()
        ]);

        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();

        $user->skills()->attach([$skillA->id, $skillB->id]);

        $this->patch("usuarios/$user->id/restaurar")
            ->assertRedirect(route('users.trashed'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'deleted_at' => null,
        ]);

    }

    /** @test */
    function Cannot_restore_a_not_trashed_user()
    {
        $this->withExceptionHandling();

        $user = factory(User::class)->create([
            'deleted_at' => null
        ]);

        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();

        $user->skills()->attach([$skillA->id, $skillB->id]);

        $this->patch('usuarios/' . $user->id . '/restaurar')
            ->assertStatus(404);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => null
        ]);
    }



}
