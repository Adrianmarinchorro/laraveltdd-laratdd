<?php

namespace Tests\Feature;

use App\Profession;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListProfessionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_shows_the_Professions_list()
    {
        factory(Profession::class)->create(['title' => 'Diseñador']);

        factory(Profession::class)->create(['title' => 'Programador']);

        factory(Profession::class)->create(['title' => 'Administrador']);

        $this->get('profesiones')
            ->assertStatus(200)
            ->assertSeeInOrder([
                'Administrador',
                'Diseñador',
                'Programador',
            ]);
    }

    /** @test */
    function it_shows_the_empty_Professions_list()
    {
        $this->get('profesiones')
            ->assertStatus(200)
            ->assertSee('No hay profesiones registradas');
    }

}
