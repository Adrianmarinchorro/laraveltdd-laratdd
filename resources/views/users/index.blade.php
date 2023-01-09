@extends('layout')

@section('title', $title)

@section('content')

    <div class="d-flex justify-content-between align-items-end mb-3">
        <h1 class="pb-1">{{ $title }}</h1>

        <p><a class="btn btn-primary" href="{{ route('users.create') }}">Nuevo usuario</a></p>
    </div>

    @if($users->isNotEmpty())
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Nombre</th>
                <th scope="col">Correo electrónico</th>
                <th scope="col">Acciones</th>
            </tr>
            </thead>
            <tbody>

            @foreach($users as $user)
                <tr>
                    <th scope="row">{{ $user->id }}</th>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->trashed())
                            <form action="{{ route('users.restore', $user->id) }}" method="post">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-link" type="submit"><span class="oi oi-pencil"></span></button>
                            </form>

                            <form action="{{ route('users.destroy', $user) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-link" type="submit"><span class="oi oi-circle-x"></span></button>
                            </form>
                        @else
                            <form action="{{ route('users.trash', $user) }}" method="post">
                                @csrf
                                @method('PATCH')
                                <a class="btn btn-link" href="{{ route('users.show', $user) }}"><span class="oi oi-eye" /></a>
                                <a class="btn btn-link" href="{{ route('users.edit', $user) }}"><span class="oi oi-pencil"/></a>
                                <button class="btn btn-link" type="submit"><span class="oi oi-trash"></span></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $users->render() }}

    @else
        <p>No hay usuarios registrados.</p>
    @endif

@endsection
