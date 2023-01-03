@extends('layout')

@section('title', 'Crear usuario');

@section('content')

    @card

        @slot('header', 'Crear nuevo usuario')

        @include('shared._errors')

        <form action="{{ route('users.store') }}" method="POST">

            @include('users._fields')

            <div class="form-group mt-4">
                <button class="btn btn-primary" type="submit">Crear usuario</button>
                <a class="btn btn-link" href="{{ route('users.index') }}">Regresar al listado de usuarios</a>
            </div>

        </form>

    @endcard
@endsection