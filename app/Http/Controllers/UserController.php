<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {

        $users = [
            'Joel',
            'Ellie',
            'Tess',
            'Tommy',
            'Bill',
        ];

        $tittle = 'Listado de usuarios';

        return view('users', compact(
                'tittle',
                'users'
            )
        );
    }

    public function show($id)
    {
        return 'Mostrando detalles del usuario: ' . $id;
    }

    public function create()
    {
        return 'Creando nuevo usuario';
    }
}