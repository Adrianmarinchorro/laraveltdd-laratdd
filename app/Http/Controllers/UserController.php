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

    public function show($id)
    {
        $user = User::findOrFail($id);

//        if($user == null) {
//            return response()->view('errors.404', [], 404);
//        }

        return view('users.show', compact('user'));
    }

    public function create()
    {
        $title = 'Creando nuevo usuario';

        return view('create', compact('title'));
    }
}