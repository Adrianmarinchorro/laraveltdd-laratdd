<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {

        $users = User::all();

        $title = 'Listado de usuarios';


//        return view('users.index')
//            ->with('users', User::all())
//            ->with('title', 'Listado de usuarios');

        return view('users.index', compact(
                'title',
                'users'
            )
        );
    }

    public function show(User $user)
    {
        // $user = User::findOrFail($id);

        // dd($user); al enlazarse el modelo a la ruta (eloquent y laravel) nos trae el objeto mediante la llave primaria

        return view('users.show', compact('user'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store()
    {

        // return redirect('/usuarios/nuevo/')->withInput();

        $data = request()->validate([
            'name' => 'required',

        ], [
            'name.required' => 'El campo nombre es obligatorio'
        ]);

//        if(empty($data['name'])) {
//            return redirect(route('users.create'))->withErrors([
//                'name' => 'el campo es obligatorio'
//            ]);
//        }

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        return redirect()->route('users.index');
    }
}