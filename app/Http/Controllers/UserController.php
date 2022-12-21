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
            'email' => 'required|email|unique:users,email', // si no se añade regla ya si es capaz de capturar el valor
            'password' => 'required|digits_between:7,30',

        ], [
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'password.required' => 'La contraseña es obligatoria',
            'password.digits_between' => 'La contraseña debe tener mas de seis caracteres'
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