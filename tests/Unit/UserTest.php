<?php

namespace Tests\Unit;


use App\{User, Login};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function gets_the_last_login_datetime_of_each_user()
    {
        $joel = factory(User::class)->create([
            'first_name' => 'joel',
        ]);

        factory(Login::class)->create([
           'user_id' => $joel->id,
            'created_at' => '2020-09-18 12:30:00',
        ]);

        factory(Login::class)->create([
            'user_id' => $joel->id,
            'created_at' => '2020-09-18 12:31:00',
        ]);

        factory(Login::class)->create([
            'user_id' => $joel->id,
            'created_at' => '2020-09-17 12:31:00',
        ]);

        $ellie = factory(User::class)->create([
            'first_name' => 'ellie',
        ]);

        factory(Login::class)->create([
            'user_id' => $ellie->id,
            'created_at' => '2020-09-15 12:00:00',
        ]);

        factory(Login::class)->create([
            'user_id' => $ellie->id,
            'created_at' => '2020-09-15 12:01:00',
        ]);

        factory(Login::class)->create([
            'user_id' => $ellie->id,
            'created_at' => '2020-09-15 11:59:59',
        ]);

        $users = User::all();

        // la libreria carbon nos permite crear fechas, $users->firstWhere() nos obtiene el primer usuario que cumpla la condicion y obtenemos a traves de la relacion
        // la propiedad created_at para compararla con el método assertEquals
        $this->assertEquals(Carbon::parse('2020-09-18 12:31:00'), $users->firstWhere('first_name', 'joel')->lastLogin->created_at);

        $this->assertEquals(Carbon::parse('2020-09-15 12:01:00'), $users->firstWhere('first_name', 'ellie')->lastLogin->created_at);

        // $this->assertTrue($users->firstWhere('first_name', 'joel')->lastLogin->created_at->eq('2020-09-18 12:31:00'))

    }
}
