@extends('layouts.app')

@section('content')
    <section class="pb-5">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
            aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a style="color: white" href="/">Home</a></li>
                <li style="color: white" class="breadcrumb-item active" aria-current="page">Profile</li>
            </ol>
        </nav>
        <div class="d-flex flex-wrap gap-2 justify-content-start text-white mt-4">
            <div class="d-lg-none col-12">
                <div class="d-flex jusitfy-content-between gap-2">
                    <img src="https://i.ibb.co/PND5cR4/new-profil.png" style="border-radius: 5px" class="img-profile mb-4"
                        alt="">
                    <div>
                        <div>Programming Languages :</div>
                        <div class="badge bg-secondary">
                            Javascript/ Typescript
                        </div>
                        <div class="badge bg-secondary">
                            CSS/SASS
                        </div>
                        <div class="badge bg-secondary">
                            Dart
                        </div>
                        <div class="badge bg-secondary">
                            Kotlin
                        </div>
                        <div class="badge bg-secondary">
                            PHP
                        </div>
                        <div class="badge bg-secondary">
                            C#
                        </div>
                        <div class="badge bg-secondary">
                            SQL
                        </div>
                        <div class="mt-3">Frameworks :</div>
                        <div class="badge bg-success">
                            React JS
                        </div>
                        <div class="badge bg-success">
                            Flutter
                        </div>
                        <div class="badge bg-success">
                            Next JS
                        </div>
                        <div class="badge bg-success">
                            Vue JS
                        </div>
                        <div class="badge bg-success">
                            DotNet
                        </div>
                        <div class="badge bg-success">
                            Tailwind
                        </div>
                        <div class="badge bg-success">
                            Bootstrap
                        </div>
                    </div>
                </div>
                <h5 class="mb-0">Name</h5>
                <h3 class="mb-2">Wisnu Adi Wardhana</h3>
                <h5 class="mb-0">Age</h5>
                <h3 class="mb-2">26 Years Old</h3>
                <h5 class="mb-0">Education</h5>
                <h3 class="mb-2">Diploma IV / Bachelor</h3>
                <h5 class="mb-0">Last status</h5>
                <h3 class="mb-2 d-flex align-items-center">
                    <div>Employeed&nbsp;&nbsp;</div>
                    <span style="background-color: green; color: white" class="badge">
                        Open to work
                    </span>
                </h3>
                <h5 class="mb-0">Email</h5>
                <h5 class="mb-2">adiwardhanawisnu@gmail.com</h5>
                <h5 class="mb-0">LinkedIn</h5>
                <h5 class="mb-2">
                    <a target="_blank" rel="noopener noreferrer"
                        href="https://www.linkedin.com/in/wisnu-adi-wardhana-560473163">https://www.linkedin.com/in/wisnu-adi-wardhana-560473163
                    </a>
                </h5>
                <h5 class="mb-0">Github</h5>
                <h5 class="mb-2">
                    <a target="_blank" rel="noopener noreferrer"
                        href="https://github.com/wisnuciwun">https://github.com/wisnuciwun
                    </a>
                </h5>
                <h5 class="mb-0">Hackerrank</h5>
                <h5 class="mb-2">
                    <a target="_blank" rel="noopener noreferrer"
                        href="https://www.hackerrank.com/adiwardhanawisnu">https://www.hackerrank.com/adiwardhanawisnu
                    </a>

                </h5>
                <h5 class="mb-0">Exercism</h5>
                <h5 class="mb-2">
                    <a target="_blank" rel="noopener noreferrer"
                        href="https://exercism.org/profiles/wisnuciwun">https://exercism.org/profiles/wisnuciwun
                    </a>
                </h5>
                <h5 class="mb-0">LeetCode</h5>
                <h5 class="mb-2">
                    <a target="_blank" rel="noopener noreferrer"
                        href="https://leetcode.com/adiwardhanawisnu">https://leetcode.com/adiwardhanawisnu
                    </a>
                </h5>
                <h5 class="mb-0">Address</h5>
                <h5 class="mb-2">Prima Swarga Residence Blok B5 No. 21, Bandung, West Java, Indonesia</h3>
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3959.7741548959116!2d107.60891487499754!3d-7.035809092966137!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zN8KwMDInMDguOSJTIDEwN8KwMzYnNDEuNCJF!5e0!3m2!1sid!2sid!4v1696234236761!5m2!1sid!2sid"
                        width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <div class="d-lg-none col-12 mt-3">
                @if (count($data) > 0)
                    <table class="table table-stripped table-dark" style="overflow-y: scroll">
                        <tr>
                            <th class="col-10">Portofolio Name</th>
                            @if (!Auth::guest())
                                <th class="col-1"></th>
                                <th class="col-1"></th>
                            @endif
                        </tr>
                        @foreach ($data as $item)
                            <tr>
                                <td>
                                    <div class="mb-2">
                                        {{ $item->folio_name }}
                                    </div>
                                    <div class="d-flex gap-1 justify-content-start align-self-end flex-wrap">
                                        @foreach (explode('#', str_replace(' ', '', $item->hashtags)) as $items)
                                            <span style="background-color: #B4B4B4; color: black"
                                                class="badge">{{ $items }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                @if (!Auth::guest())
                                    <td><a class="btn btn-primary" href="/post/{{ $item->id }}/edit">Edit</a></td>
                                    <td>
                                        {{ Form::open(['route' => ['post.destroy', $item->id], 'method' => 'DELETE']) }}
                                        {{ Form::submit('Delete', ['class' => 'btn btn-danger']) }}
                                        {{ Form::close() }}
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                @else
                    <div>You dont have any portofolio yet, <a href="/post/create" class="btn btn-primary">create one</a>
                    </div>
                @endif
            </div>
            <div class="d-none d-lg-block col-5">
                <div class="d-flex jusitfy-content-between gap-2">
                    <img src="https://i.ibb.co/PND5cR4/new-profil.png" style="border-radius: 5px" class="img-profile mb-4"
                        alt="">
                    <div>
                        <div>Programming Languages :</div>
                        <div class="badge bg-secondary">
                            Javascript/ Typescript
                        </div>
                        <div class="badge bg-secondary">
                            CSS/SASS
                        </div>
                        <div class="badge bg-secondary">
                            Dart
                        </div>
                        <div class="badge bg-secondary">
                            Kotlin
                        </div>
                        <div class="badge bg-secondary">
                            PHP
                        </div>
                        <div class="badge bg-secondary">
                            C#
                        </div>
                        <div class="badge bg-secondary">
                            SQL
                        </div>
                        <div class="mt-3">Frameworks :</div>
                        <div class="badge bg-success">
                            React JS
                        </div>
                        <div class="badge bg-success">
                            Flutter
                        </div>
                        <div class="badge bg-success">
                            Next JS
                        </div>
                        <div class="badge bg-success">
                            Vue JS
                        </div>
                        <div class="badge bg-success">
                            DotNet
                        </div>
                        <div class="badge bg-success">
                            Tailwind
                        </div>
                        <div class="badge bg-success">
                            Bootstrap
                        </div>
                    </div>
                </div>
                <h5 class="mb-0">Name</h5>
                <h3 class="mb-2">Wisnu Adi Wardhana</h3>
                <h5 class="mb-0">Age</h5>
                <h3 class="mb-2">26 Years Old</h3>
                <h5 class="mb-0">Education</h5>
                <h3 class="mb-2">Diploma IV / Bachelor</h3>
                <h5 class="mb-0">Last status</h5>
                <h3 class="mb-2 d-flex align-items-center">
                    <div>Employeed&nbsp;&nbsp;</div>
                    <span style="background-color: green; color: white" class="badge">
                        Open to work
                    </span>
                </h3>
                <h5 class="mb-0">Email</h5>
                <h5 class="mb-2">adiwardhanawisnu@gmail.com</h5>
                <h5 class="mb-0">LinkedIn</h5>
                <h5 class="mb-2">
                    <a target="_blank" rel="noopener noreferrer"
                        href="https://www.linkedin.com/in/wisnu-adi-wardhana-560473163">https://www.linkedin.com/in/wisnu-adi-wardhana-560473163
                    </a>
                </h5>
                <h5 class="mb-0">Github</h5>
                <h5 class="mb-2">
                    <a target="_blank" rel="noopener noreferrer"
                        href="https://github.com/wisnuciwun">https://github.com/wisnuciwun
                    </a>
                </h5>
                <h5 class="mb-0">Hackerrank</h5>
                <h5 class="mb-2">
                    <a target="_blank" rel="noopener noreferrer"
                        href="https://www.hackerrank.com/adiwardhanawisnu">https://www.hackerrank.com/adiwardhanawisnu
                    </a>

                </h5>
                <h5 class="mb-0">Exercism</h5>
                <h5 class="mb-2">
                    <a target="_blank" rel="noopener noreferrer"
                        href="https://exercism.org/profiles/wisnuciwun">https://exercism.org/profiles/wisnuciwun
                    </a>
                </h5>
                <h5 class="mb-0">LeetCode</h5>
                <h5 class="mb-2">
                    <a target="_blank" rel="noopener noreferrer"
                        href="https://leetcode.com/adiwardhanawisnu">https://leetcode.com/adiwardhanawisnu
                    </a>
                </h5>
                <h5 class="mb-0">Address</h5>
                <h5 class="mb-2">Prima Swarga Residence Blok B5 No. 21, Bandung, West Java, Indonesia</h3>
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3959.7741548959116!2d107.60891487499754!3d-7.035809092966137!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zN8KwMDInMDguOSJTIDEwN8KwMzYnNDEuNCJF!5e0!3m2!1sid!2sid!4v1696234236761!5m2!1sid!2sid"
                        width="90%" height="250" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <div class="d-none d-lg-block col-6">
                @if (count($data) > 0)
                    <table class="table table-stripped table-dark" style="overflow-y: scroll">
                        <tr>
                            <th class="col-10">Portofolio Name</th>
                            @if (!Auth::guest())
                                <th class="col-1"></th>
                                <th class="col-1"></th>
                            @endif
                        </tr>
                        @foreach ($data as $item)
                            <tr>
                                <td>
                                    <div class="mb-2">
                                        {{ $item->folio_name }}
                                    </div>
                                    <div class="d-flex gap-1 justify-content-start align-self-end flex-wrap">
                                        @foreach (explode('#', str_replace(' ', '', $item->hashtags)) as $items)
                                            <span style="background-color: #B4B4B4; color: black"
                                                class="badge">{{ $items }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                @if (!Auth::guest())
                                    <td><a class="btn btn-primary" href="/post/{{ $item->id }}/edit">Edit</a></td>
                                    <td>
                                        {{ Form::open(['route' => ['post.destroy', $item->id], 'method' => 'DELETE']) }}
                                        {{ Form::submit('Delete', ['class' => 'btn btn-danger']) }}
                                        {{ Form::close() }}
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                @else
                    <div>You dont have any portofolio yet, <a href="/post/create" class="btn btn-primary">create one</a>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
