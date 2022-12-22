@extends('layout')

@section('title', 'Crear usuario');

@section('content')
    <h1>Editar usuario:</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <h5>Por favor corrige los errores de debajo:</h5>
            <ul>
                @foreach($errors->all() as $error)

                    <li>{{$error}}</li>

                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST">
        {{ csrf_field() }}

        <label for="name">Nombre:</label>
        <input type="text" name="name" id="name" placeholder="Pepe Pérez" value="{{ old('name', $user->name) }}">

        <br>

        <label for="email">Correo electrónico:</label>
        <input type="email" name="email" id="email" placeholder="pedro@example.com" value="{{ old('email', $user->email) }}">

        <br>

        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" placeholder="Mayor a 6 caracteres">

        <br>

        <button type="submit">Actualizar usuario</button>
    </form>

    <p>
        <a href="{{ route('users.index') }}">Regresar al listado de usuarios</a>
    </p>

@endsection