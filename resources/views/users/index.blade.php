@extends('layout')

@section('title',  trans("users.title.{$view}"))

@section('content')

    <div class="d-flex justify-content-between align-items-end mb-3">
        <h1 class="pb-1">{{ trans("users.title.{$view}") }}</h1>

        @if($view == 'index')
            <p><a class="btn btn-outline-dark" href="{{ route('users.trashed') }}">Ver papelera</a></p>
            <p><a class="btn btn-primary" href="{{ route('users.create') }}">Nuevo usuario</a></p>
        @else
                <p><a class="btn btn-outline-dark" href="{{ route('users.index') }}">Regresar al listado de usuarios</a></p>
        @endif

    </div>

    @includeWhen($view === 'index', 'users._filters')

    @if($users->isNotEmpty())

        <div class="table-responsive-lg table-striped">
            <table class="table table-sm">
                <thead class="thead-dark">
                <tr>
                    <th scope="col"># <span class="oi oi-caret-bottom"></span><span class="oi oi-caret-top"></span></th>
                    <th scope="col" ><a href="{{ $sortable->url('first_name') }}" class="{{ $sortable->classes('first_name') }}">Nombre</a></th>
                    <th scope="col"><a href="{{ $sortable->url('email') }}" class="{{ $sortable->classes('email') }}">Correo</a></th>
                    <th scope="col"><a href="{{ $sortable->url('created_at') }}" class="{{ $sortable->classes('created_at') }}">Fechas</a></th>
                    <th scope="col" class="text-right th-actions">Acciones</th>
                </tr>
                </thead>
                <tbody>

                @each('users._row', $users, 'user')
                </tbody>
            </table>

            {{ $users->links() }}

            @else
                <p>No hay usuarios registrados.</p>
    @endif
@endsection

@section('sidebar')
    @parent
@endsection