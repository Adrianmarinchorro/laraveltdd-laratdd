<?php

namespace App\Http\Controllers;

use App\{Http\Requests\CreateUserRequest, Http\Requests\UpdateUserRequest, Profession, Role, Skill, User, UserProfile};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {

        $users = User::all();

        $title = 'Listado de usuarios';

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

    protected function form($view, User $user)
    {
        return view($view, [
            'user' => $user,
            'professions' => Profession::orderBy('title', 'ASC')->get(),
            'skills' => Skill::orderBy('name', 'ASC')->get(),
            'roles' => trans('users.roles'),
        ]);
    }

    public function create()
    {
       return $this->form('users.create', new User);
    }

    public function store(CreateUserRequest $request)
    {
        $request->createUser();

        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        return $this->form('users.edit', $user);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $request->updateUser($user);

        return redirect()->route('users.show', ['user' => $user]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index');
    }
}