@extends('layouts.app')

@section('content')
    <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
        aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a style="color: white" href="/">Home</a></li>
            <li style="color: white" class="breadcrumb-item active" aria-current="page">Portofolio</li>
        </ol>
    </nav>
    <div class="d-flex flex-wrap gap-4 text-light">
        @if (count($data) > 0)
            @foreach ($data as $item)
                <div class="card text-white bg-dark" style="height: 400px; width: 32%; overflow: hidden">
                    @if (str_contains($item->image_url, 'https'))
                        <img style="height: 200px; object-fit: cover;" class="card-img-top w-100"
                            src="{{ $item->image_url }}" alt="Card image cap">
                    @else
                        <img style="height: 200px; object-fit: cover;" class="card-img-top w-100"
                            src="/storage/cover_images/{{ $item->image_url }}" alt="">
                    @endif
                    {{-- <img class="card-img-top w-100" this is how to use if else inside src
                        src="@php if (str_contains($item->image_url, 'https')) {
                            echo asset($item->image_url);
                        }
                        else {
                            echo "/storage/cover_images/{{ $item->image_url }}";
                        } @endphp"
                        alt="Card image cap"> --}}
                    <div class="card-body h-100 d-flex flex-wrap">
                        <div class="w-100">
                            <div onclick="window.location.replace('/post/{{ $item->id }}')" style="cursor:pointer"
                                class="card-title">
                                <h3>{{ $item->folio_name }}</h3>
                            </div>
                            <div class="card-text w-100">
                                {{ $item->short_desc }}
                            </div>
                        </div>
                        <div class="d-flex gap-1 justify-content-start align-self-end">
                            @foreach (explode('#', str_replace(' ', '', $item->hashtags)) as $item)
                                <span style="background-color: #B4B4B4; color: black"
                                    class="badge badge-danger">{{ $item }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <p>No portofolio found</p>
        @endif
    </div>
    <div class="d-flex mt-3">
        {{-- add inside links() to use any style or bootstrap --}}
        {{ $data->links('pagination::bootstrap-4') }}
    </div>
@endsection
