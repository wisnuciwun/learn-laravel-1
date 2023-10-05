@extends('layouts.app')

@section('content')
    <section class="pb-5">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
            aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a style="color: white" href="/">Home</a></li>
                <li class="breadcrumb-item"><a style="color: white" href="/post">Portofolio</a></li>
                <li style="color: white" class="breadcrumb-item active" aria-current="page">Detail</li>
            </ol>
        </nav>
        <div class="card text-white bg-dark w-100 border-light">
            <img src="/bg-detail.jpg" class="w-100 position-absolute"
                style="object-fit: cover; z-index: 0; mix-blend-mode:saturation; height: 100%" alt="">

            <div style="z-index: 1" class="card-body position-relative">
                <h1 style="cursor:pointer" class="card-title mb-4">
                    {{ $data->folio_name }}</h1>

                <div class="d-lg-flex d-none justify-content-between gap-3 align-items-start">
                    <div class="w-50">
                        <div style="min-height: 500px;" class="d-flex justify-content-center align-items-start">
                            <img style="object-fit: scale-down; width: 100%;" src="{{ $data->image_url }}" alt="">
                        </div>
                        <div class="mt-3 d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined">
                                public
                            </span>
                            <a href="{{ $data->url }}" target="_blank" rel="noopener noreferrer" class="text-white"
                                style="font-size: 18px;">
                                {{ $data->url }}
                            </a>
                        </div>
                    </div>
                    <div class="w-50" class="card-text">
                        {{-- this is how to parse  html from ckeditor as html code --}}
                        {!! $data->description !!}
                    </div>
                </div>

                <div class="d-lg-none d-flex justify-content-center flex-wrap align-items-start">
                    <div class="w-100">
                        <div class="d-flex justify-content-center align-items-start">
                            <img style="object-fit: scale-down; width: 100%; height: auto" src="{{ $data->image_url }}"
                                alt="">
                        </div>
                        <div class="mt-3 mb-3 d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined">
                                public
                            </span>
                            <a href="{{ $data->url }}" target="_blank" rel="noopener noreferrer" class="text-white"
                                style="font-size: 18px;">
                                {{ $data->url }}
                            </a>
                        </div>
                    </div>
                    <div class="w-100" class="card-text">
                        {{-- this is how to parse  html from ckeditor as html code --}}
                        {!! $data->description !!}
                    </div>
                </div>

            </div>
        </div>
        {{-- !! Form::open(['route' => 'post.store', 'method' => 'POST']) !!} --}}
        {{-- add !Auth::guest() to hide for guest, but all user can see it --}}
        <div style="z-index: 9; position: relative" class="mt-3 d-flex align-items-center gap-2">
            @if (!Auth::guest())
                {{-- add Auth::user()->id to hide securely depend on login user_id --}}
                @if (Auth::user()->id == $data->user_id)
                    {{ Form::open(['route' => ['post.destroy', $data->id], 'method' => 'DELETE']) }}
                    {{ Form::submit('Delete', ['class' => 'btn btn-danger']) }}
                    {{ Form::close() }}
                    <a href="/post/{{ $data->id }}/edit" class="btn btn-dark">
                        Edit
                    </a>
                @endif
            @endif
        </div>
    </section>
@endsection
