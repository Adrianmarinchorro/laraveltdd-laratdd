<?php

use App\{Login, Skill, Team, User, Profession, UserProfile};
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
                    // factory(User::class)->create
        $admin = User::create([
            'team_id' => $this->teams->firstWhere('name', 'IES Ingeniero')->id,// para obtener 1 solo equipo con dicho nombre
            'first_name' => 'Adrián',
            'last_name' => 'Marín',
            'email' => 'adri@gmail.com',
            'password' => bcrypt('123'),
            'role' => 'admin',
            'created_at' => now(), // ya no hace falta ponerle un dia más ya que al resto le vamos a restar días y por tanto este sera el mas moderno.
            'active' => true,
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
            'active' => rand(0, 3) ? true : false,
            'created_at' => now()->subDays(rand(1, 90)), // le vamos a restar la fecha actual un numero random de dias entre 1 y 90
        ]);

        $user->skills()->attach($this->skills->random(rand(0, 7)));

        $user->profile()->update([
            'profession_id' => rand(0, 2) ? $this->professions->random()->id : null,
        ]);

        factory(Login::class)->times(rand(1,10))->create([
            'user_id' => $user->id,
        ]);
    }
}
