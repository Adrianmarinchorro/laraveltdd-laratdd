@extends('layout')

@section('title', "Usuario {$id}")

@section('content')
        <h1>Usuario #{{ $id }}</h1>

        Mostrando detalles del usuario: {{ $id }}
@endsection
