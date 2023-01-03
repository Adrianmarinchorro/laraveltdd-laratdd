@extends('layout')

@section('title', 'Crear usuario');

@section('content')

    @card

        @slot('header', 'Editar usuario')

        @include('shared._errors')

        <form action="{{ route('users.update', ['user' => $user]) }}" method="POST">
            {{ method_field('PUT') }}

            @include('users._fields')

            <div class="form-group mt-4">
                <button class="btn btn-primary" type="submit">Actualizar usuario</button>
                <a class="btn btn-link" href="{{ route('users.index') }}">Regresar al listado de usuarios</a>
            </div>

        </form>

    @endcard

@endsection