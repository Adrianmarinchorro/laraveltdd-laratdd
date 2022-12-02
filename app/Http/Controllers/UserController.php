<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {

        if (request()->has('empty')) {
            $users = [];
        } else {
            $users = [
                'Joel',
                'Ellie',
                'Tess',
                'Tommy',
                'Bill',
            ];
        }

        $title = 'Listado de usuarios';

        return view('users', compact(
                'title',
                'users'
            )
        );
    }

    public function show($id)
    {
        $title = 'Mostrando detalles del usuario: ' . $id;

        return view('show', compact('title'));
    }

    public function create()
    {
        $title = 'Creando nuevo usuario';

        return view('create', compact('title'));
    }
}