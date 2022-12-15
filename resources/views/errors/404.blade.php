@extends('layout')

@section('title', "Página no encontrada")

@section('content')


    <h1>Página no encontrada</h1>

    <p>
        <a href="{{ route('users.index') }}">Regresar al listado de usuarios</a>
    </p>
@endsection
