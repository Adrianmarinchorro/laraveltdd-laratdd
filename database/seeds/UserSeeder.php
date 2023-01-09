<?php

use App\Skill;
use App\User;
use App\Profession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $professions = Profession::all();

        $skills = Skill::all();

        $user = factory(User::class)->create([
            'name' => 'Adrián Marín',
            'email' => 'adri@gmail.com',
            'password' => bcrypt('123'),
            'role' => 'admin',
            'created_at' => now()->addDay(), // creado en una fecha en el futuro para que salga el primero
        ]);

        $user->profile()->create([
            'bio' => 'Programador',
            'profession_id' =>  $professions->where('title', 'Desarrollador back-end')->first()->id,
        ]);

        // ->where('title', 'Desarrollador back-end')->first()->id, == ->firstWhere('title', 'Desarrollador back-end')->id

        // pasamos el listado de profesiones y habilidades a esta funcion anonima
        factory(User::class, 999)->create()->each(function ($user) use ($professions, $skills) {

            $randomSkills = $skills->random(rand(0, 7)); // juntamos habilidades aleatorias, numero entre 0 y 7 (tenemos 7 habilidades)

            $user->skills()->attach($randomSkills); //luego vamos a juntar estas habilidades aleatorias a este modelo de $user con la relacion skills() con el metodo attach()

            factory(\App\UserProfile::class)->create([
                'user_id' => $user->id,
                // una profesion de manera aelatoria 2/3 tienen profesion (aleatoria) y el resto null puesto que es campo nullable
                'profession_id' => rand(0, 2) ? $professions->random()->id : null,
            ]);
        });

    }
}
