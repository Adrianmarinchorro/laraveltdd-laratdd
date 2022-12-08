<?php

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

        $professionId = DB::table('professions')
            ->whereTitle('Desarrollador back-end') // metodo magico
            // ->where('title', 'Desarrollador back-end')
            ->value('id')
        ;

        // dd($profession->id); // $professions[0]

        DB::table('users')->insert([
            'name' => 'adrian',
            'email' => 'adri@gmail.com',
            'password' => bcrypt('123'),
            'profession_id' => $professionId, // $profession->id
        ]);
    }
}
