<?php

namespace App\Http\Controllers;

use App\Profession;
use App\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = User::first();

        //$user = auth()->user(); tipicamente llamariamos a auth()->user() para obtener el usuario conectado

        return view('profiles.edit', [
            'user' => $user,
            'professions' => Profession::orderBy('title')->get()
        ]);
    }

    public function update(Request $request)
    {
        $user = User::first(); // or auth()->user();
//
//        $data = $request->all(); // TODO: add validation.
//
//        unset($data['password']);

        // con esto ya no tenemos que preocuparnos de errores ni trabajr con all()
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
        ]);

        $user->profile->update([
            'bio' => $request->bio,
            'twitter' => $request->twitter,
            'profession_id' => $request->profession_id,
        ]);

        return back();
    }
    
}
