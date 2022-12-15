@extends('layout')

@section('title', "Usuario #{$user->id}")

@section('content')
        <h1>Usuario #{{ $user->id }}</h1>

        <p>Nombre del usuario: {{ $user->name }}</p>
        <p>Correo electrónico: {{ $user->email }}</p>

        <!-- linea ejercicio de ejemplo para ampliar los datos del usuario -->
        <p>Profesión: {{ $user->profession->title ?? 'Sin profesion' }}</p>

        <p>
                <a href="{{ route('users.index') }}">Regresar al listado de usuarios</a>
        </p>
@endsection
