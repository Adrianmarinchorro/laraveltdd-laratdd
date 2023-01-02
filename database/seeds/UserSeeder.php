<?php

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
        $professionId = Profession::where('title', 'Desarrollador back-end')->value('id');

        $user = factory(User::class)->create([
            'name' => 'Adrián Marín',
            'email' => 'adri@gmail.com',
            'password' => bcrypt('123'),
            'role' => 'admin',
        ]);

        $user->profile()->create([
            'bio' => 'Programador',
            'profession_id' =>  $professionId,
        ]);

        factory(User::class, 29)->create()->each(function ($user){
            $user->profile()->create(
                factory(\App\UserProfile::class)->raw()
            );
        });

    }
}
