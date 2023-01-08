<?php

namespace Tests\Feature;

use App\Skill;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListSkillsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_shows_the_Skills_list()
    {
        factory(Skill::class)->create(['name' => 'PHP']);
        factory(Skill::class)->create(['name' => 'JS']);
        factory(Skill::class)->create(['name' => 'CSS']);

        $this->get('habilidades/')
            ->assertStatus(200)
            ->assertSeeInOrder([
                'CSS',
                'JS',
                'PHP',
            ]);
    }

    /** @test */
    function it_shows_the_empty_Skills_list()
    {
        $this->get('habilidades/')
            ->assertStatus(200)
            ->assertSee('No hay habilidades registradas');
    }
}
