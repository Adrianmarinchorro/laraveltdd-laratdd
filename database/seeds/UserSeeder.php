<?php

use App\{Skill, Team, User, Profession, UserProfile};
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{

    protected $teams;
    protected $skills;
    protected $professions;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->fetchRelations();

        $this->createAdmin();

        // se crean los usuarios 1 por 1 hasta llegar a 999
        foreach(range(1,999) as $i){
            $this->createRandomUser();
        }
    }

    public function fetchRelations(): void
    {
        $this->professions = Profession::all();

        $this->skills = Skill::all();

        $this->teams = Team::all();
    }


    public function createAdmin(): void
    {
        $admin = factory(User::class)->create([
            'team_id' => $this->teams->firstWhere('name', 'IES Ingeniero'),// para obtener 1 solo equipo con dicho nombre
            'first_name' => 'Adrián',
            'last_name' => 'Marín',
            'email' => 'adri@gmail.com',
            'password' => bcrypt('123'),
            'role' => 'admin',
            'created_at' => now()->addDay(), // creado en una fecha en el futuro para que salga el primero
        ]);

        $admin->skills()->attach($this->skills);

        $admin->profile()->create([
            'bio' => 'Programador',
            'profession_id' => $this->professions->where('title', 'Desarrollador back-end')->first()->id,
        ]);
    }

    public function createRandomUser(): void
    {
        $user = factory(User::class)->create([
            'team_id' => rand(0, 2) ? null : $this->teams->random()->id, // asignar cualquier equipo de manera aleatoria a este usuario
        ]);

        $user->skills()->attach($this->skills->random(rand(0, 7)));

        factory(UserProfile::class)->create([
            'user_id' => $user->id,
            'profession_id' => rand(0, 2) ? $this->professions->random()->id : null,
        ]);
    }
}
