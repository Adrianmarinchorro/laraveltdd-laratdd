<?php

namespace App\Http\Controllers;

use App\{Profession, Role, Skill, User};
use App\Http\Requests\{CreateUserRequest, UpdateUserRequest};

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->with('team', 'skills', 'profile.profession')
            ->when(request('team'), function ($query, $team){
                if($team === 'with_team'){
                    $query->has('team');
                } else if ($team === 'without_team'){
                    $query->doesntHave('team');
                }
            })
            ->byState(request('state'))
            ->byRole(request('role'))
            ->search(request('search')) //llamamos al metodo sin la palabra scope
            ->orderBy('created_at', 'DESC')
            ->paginate();

        $users->appends(request(['search', 'team']));


        return view('users.index', [
            'users' => $users,
            'skills' => Skill::orderBy('name')->get(),
            'view' => 'index',
            'checkedSkills' => collect(request('skills')),
        ]);
    }

    public function trashed()
    {
        return view('users.index', [
            'users' => User::onlyTrashed()->paginate(),
            'view' => 'trash',
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