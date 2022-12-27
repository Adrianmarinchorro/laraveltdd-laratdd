<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
            'password' => 'required|min:7',

        ], [
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.unique' => 'El correo electrónico debe ser único',
            'email.email' => 'El correo electrónico debe ser válido',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener mas de seis caracteres'
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(User $user)
    {
        $data = request()->validate([
            'name' => 'required',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => '',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El correo electrónico debe ser valido',
            'email.unique' => 'El correo electrónico debe ser único',
        ]);

        if($data['password'] != null) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }


        $user->update($data);

        return redirect()->route('users.show', ['user' => $user]);
    }

    public function destroy(User $user)
    {
        $user->delete();

       return redirect()->route('users.index');
    }
}