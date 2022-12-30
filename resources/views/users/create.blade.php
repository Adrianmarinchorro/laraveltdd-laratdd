@extends('layout')

@section('title', 'Crear usuario');

@section('content')
    <div class="card">
        <div class="card-header h4">
            Crear nuevo usuario
        </div>

        <div class="card-body">
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
                {{-- {!! csrf_field() !!}--}}

                <div class="form-group">
                    <label for="name">Nombre:</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Pepe Pérez" value="{{ old('name') }}">
                </div>

                <div class="form-group">
                    <label for="email">Correo electrónico:</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="pedro@example.com" value="{{ old('email') }}">

                </div>

                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Mayor a 6 caracteres">

                </div>

                <div class="form-group">
                    <label for="bio">Bio:</label>
                    <textarea class="form-control" name="bio" id="bio">{{ old('bio') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="profession_id">Profesión:</label>
                    <select name="profession_id" id="profession_id" class="form-control">
                            <option value="">Seleccione una profesión</option>
                    @foreach($professions as $profession)
                            <option value="{{ $profession->id }}"{{ old('profession_id') == $profession->id ? ' selected' : '' }}>
                                {{ $profession->title }}
                            </option>
                    @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="twitter">Twitter:</label>
                    <input type="text" class="form-control" name="twitter" id="twitter" placeholder="Url de tu usuario de twitter" value="{{ old('twitter') }}">
                </div>

                <button class="btn btn-primary" type="submit">Crear usuario</button>
                <a class="btn btn-link" href="{{ route('users.index') }}">Regresar al listado de usuarios</a>
            </form>
        </div>
    </div>
@endsection