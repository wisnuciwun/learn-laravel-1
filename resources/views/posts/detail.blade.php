@extends('layouts.app')

@section('content')
    {{-- <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="/post">Portofolio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail</li>
        </ol>
    </nav> --}}
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
        aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a style="color: white" href="/">Home</a></li>
            <li class="breadcrumb-item"><a style="color: white" href="/post">Portofolio</a></li>
            <li style="color: white" class="breadcrumb-item active" aria-current="page">Detail</li>
        </ol>
    </nav>
    <div class="card text-white bg-dark w-100">
        {{-- <img class="card-img-top w-100" src={{ $data->image_url }} alt="Card image cap"> --}}
        <div class="card-body">
            <div style="cursor:pointer" class="card-title">
                {{ $data->folio_name }}</div>
            <div class="card-text">
                {{-- this is how to parse  html from ckeditor as html code --}}
                {!! $data->description !!}
            </div>
        </div>
    </div>
    {{-- !! Form::open(['route' => 'post.store', 'method' => 'POST']) !!} --}}
    {{-- add !Auth::guest() to hide for guest, but all user can see it --}}
    @if (!Auth::guest())
        {{-- add Auth::user()->id to hide securely depend on login user_id --}}
        @if (Auth::user()->id == $data->user_id)
            {{ Form::open(['route', 'post.destroy', $data->id, 'class' => 'btn btn-danger', 'method' => 'POST']) }}
            {{ Form::hidden('_method', 'DELETE') }}
            {{ Form::submit('Delete', ['class' => 'btn btn-danger']) }}
            {{ Form::close() }}
            {{-- <button onclick="window.location.replace('/post/{{ $data->id }}/delete')" class="btn btn-danger mt-2">
    Delete
</button> --}}
            <button onclick="window.location.replace('/post/{{ $data->id }}/edit')" class="btn btn-dark mt-2">
                Edit
            </button>
        @endif
    @endif
@endsection
