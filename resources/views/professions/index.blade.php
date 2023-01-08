@extends('layout')

@section('title', $title)

@section('content')

    <div class="d-flex justify-content-between align-items-end mb-3">
        <h1 class="pb-1">{{ $title }}</h1>
    </div>

    @if($professions->isNotEmpty())
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">titulo</th>
                <th scope="col">Perfiles</th>
                <th scope="col">Acciones</th>
            </tr>
            </thead>
            <tbody>

            @foreach($professions as $profession)
                <tr>
                    <th scope="row">{{ $profession->id }}</th>
                    <td>{{ $profession->title }}</td>
                    <td>{{ $profession->profiles_count }}</td>
                    <td>

                        @if($profession->profiles_count == 0)
                            <form action="{{ route('profession.destroy', $profession) }}" method="post">
                                @csrf
                                {{--{{ csrf_field() }}--}}
                                @method('DELETE')
                                {{--{{ method_field('DELETE') }}--}}
                                <button class="btn btn-link" type="submit"><span class="oi oi-trash"></span></button>
                            </form>
                        @endif

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <p>No hay profesiones registradas.</p>
    @endif

@endsection
