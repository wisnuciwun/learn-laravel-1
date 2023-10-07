@extends('layouts.app')

@section('content')
    <section class="pb-5 fade-in">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
            aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a style="color: white" href="/">Home</a></li>
                <li class="breadcrumb-item"><a style="color: white" href="/post">Portofolio</a></li>
            </ol>
        </nav>
        <div class="text-light">
            <form class="w-100 mb-3" method="GET" action="{{ route('post.index') }}">
                <div class="d-flex gap-2">
                    {{-- @csrf --}}
                    <div class="input-group">
                        <input value="{{ isset($_GET['keyword']) ? $_GET['keyword'] : '' }}" name='keyword' type="text"
                            class="form-control bg-dark text-light" placeholder="Search project"
                            aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-outline-light text-light"
                                style="margin-left: 0px; border-radius: 0px 5px 5px 0px" type="button">Cari</button>
                        </div>
                    </div>
                    <select onchange="this.form.submit()" name="framework" class="form-select w-25 bg-dark text-light">
                        <option selected value="">Framework</option>
                        <option value="react">React JS</option>
                        <option value="next">Next JS</option>
                        <option value="vue">Vue JS</option>
                        <option value="net">.Net</option>
                        <option value="node">Node JS</option>
                        <option value="laravel">Laravel</option>
                    </select>
                    <select onchange="this.form.submit()" name="scope" class="form-select bg-dark text-light w-25">
                        <option selected value="">Project Scope</option>
                        <option value="fulltime">Fulltime</option>
                        <option value="freelance">Freelance</option>
                        <option value="learning">Learning/Course</option>
                        <option value="lib">Library/Component</option>
                    </select>
                </div>
            </form>
            <div class="d-lg-flex d-none flex-wrap gap-4">
                @if (count($data) > 0)
                    @foreach ($data as $item)
                        <a href="{{ route('post.show', $item->id) }}"
                            class="card text-white bg-dark border-light card-portofolio-lg text-decoration-none">
                            <div class="position-relative">
                                @if (str_contains($item->image_url, 'https'))
                                    <img style="height: 200px; object-fit: cover;" class="card-img-top w-100"
                                        src="{{ $item->image_url }}" alt="Card image cap">
                                @else
                                    <img style="height: 200px; object-fit: cover;" class="card-img-top w-100"
                                        src="/storage/cover_images/{{ $item->image_url }}" alt="">
                                @endif
                                <span
                                    style="background-color: black; color: white; bottom: 0; right: 0; margin-bottom: 5px; margin-right: 5px"
                                    class="badge badge-danger position-absolute">Release at {{ $item->created_at }}</span>
                            </div>

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
                                    <div style="cursor:pointer" class="card-title">
                                        <h3>{{ $item->folio_name }}</h3>
                                    </div>
                                    <div class="card-text w-100 text-overflow-three">
                                        {{ $item->short_desc }}
                                    </div>
                                </div>
                                <div style="overflow-y: auto" class="d-flex gap-1 justify-content-start align-self-end">
                                    @foreach (explode('#', str_replace(' ', '', $item->hashtags)) as $item)
                                        <span style="background-color: #B4B4B4; color: black"
                                            class="badge badge-danger">{{ $item }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </a>
                    @endforeach
                @else
                    <p>No portofolio found</p>
                @endif
            </div>

            <div class="d-lg-none d-flex flex-wrap gap-4">
                @if (count($data) > 0)
                    @foreach ($data as $item)
                        <div class="card text-white bg-dark border-light"
                            style="height: 400px; width: 100%; overflow: hidden">
                            <div class="position-relative">
                                @if (str_contains($item->image_url, 'https'))
                                    <img style="height: 200px; object-fit: cover;" class="card-img-top w-100"
                                        src="{{ $item->image_url }}" alt="Card image cap">
                                @else
                                    <img style="height: 200px; object-fit: cover;" class="card-img-top w-100"
                                        src="/storage/cover_images/{{ $item->image_url }}" alt="">
                                @endif
                                <span
                                    style="background-color: black; color: white; bottom: 0; right: 0; margin-bottom: 5px; margin-right: 5px"
                                    class="badge badge-danger position-absolute">Release at {{ $item->created_at }}</span>
                            </div>
                            <div class="card-body h-100 d-flex flex-wrap">
                                <div class="w-100">
                                    <div onclick="window.location.replace('/post/{{ $item->id }}')"
                                        style="cursor:pointer" class="card-title">
                                        <h3>{{ $item->folio_name }}</h3>
                                    </div>
                                    <div class="card-text w-100 text-overflow-three">
                                        {{ $item->short_desc }}
                                    </div>
                                </div>
                                <div style="overflow-y: auto" class="d-flex gap-1 justify-content-start align-self-end">
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

        </div>
        <div class="d-flex justify-content-center mt-3">
            {{-- add inside links() to use any style or bootstrap --}}
            {{ $data->links('pagination::bootstrap-4') }}
        </div>
    </section>
@endsection
