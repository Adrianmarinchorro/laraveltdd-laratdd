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
        return view('users.index', [
            'title' => 'Listado de usuarios',
            'users' => User::orderBy('created_at', 'DESC')->paginate(),
        ]);
    }

    public function trashed()
    {
        return view('users.index', [
            'title' => 'Listado de usuarios en papelera',
            'users' => User::onlyTrashed()->get(),
        ]);
    }

    public function show(User $user)
    {
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

    public function trash(User $user)
    {
        $user->delete();
        $user->profile()->delete(); // elimina el perfil de forma logica.

        return redirect()->route('users.index');
    }

    public function destroy(int $id)
    {
        $user = User::onlyTrashed()->where('id', $id)->firstOrFail();

        $user->forceDelete();

        return redirect()->route('users.trashed');
    }

    public function restore(int $id)
    {
        $user = User::onlyTrashed()->where('id', $id)->firstOrFail();

        $user->restore();
        $user->profile()->restore();

        return redirect()->route('users.trashed');
    }

}