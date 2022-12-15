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
        //$professions = DB::select('SELECT id FROM professions WHERE title = ? LIMIT 0,1', ['Desarrollador back-end']);

//        $profession = DB::table('professions')
//            ->select('id')
//            ->where('title', '=', 'Desarrollador back-end')
//            ->first()
//            // ->take(1)->get()
//        ;


        // el metodo where no esta incluido
        $professionId = Profession::where('title', 'Desarrollador back-end')->value('id');

        factory(User::class)->create([
            'name' => 'Adrián Marín',
            'email' => 'adri@gmail.com',
            'password' => bcrypt('123'),
            'profession_id' => $professionId, // $profession->id
            'is_admin' => true,
        ]);

        factory(User::class)->create([
           'profession_id' => $professionId,
        ]);

        factory(User::class, 48)->create();

    }
}
