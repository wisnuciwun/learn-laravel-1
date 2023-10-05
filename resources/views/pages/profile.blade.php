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
        <div class="d-flex flex-wrap justify-content-start text-white mt-4 w-100">
            <div class="d-lg-none col-12">
                <div class="position-relative mb-3">
                    <img src="https://i.ibb.co/PND5cR4/new-profil.png" style="border-radius: 4px;" class="w-100"
                        alt="">
                    <h2 style="background-color: green; color: white; position: absolute; bottom: 0; right: 0; margin-right: 6px; margin-bottom: 6px; font-size: 18px"
                        class="badge position-absolute">
                        #OPENTOWORK
                    </h2>
                </div>
                <h5 class="mb-0">Name</h5>
                <h3 class="mb-2"><u>Wisnu Adi Wardhana</u></h3>
                <h5 class="mb-0">Age</h5>
                <h3 class="mb-2"><u>26 y/o</u></h3>
                <h5 class="mb-0">Work status</h5>
                <h3 class="mb-2">
                    <u>Employeed</u>
                </h3>
                <h5 class="mb-0">Martial status</h5>
                <h3 class="mb-2">
                    <u>Married</u>
                </h3>
                <h5 class="mb-0">Nationality</h5>
                <h3 class="mb-2">
                    <u>Indonesia</u>
                </h3>
                </h3>
                <div class="mb-3 mt-3">
                    <h5>Programming Languages</h5>
                    <div class="badge bg-light text-dark">
                        Javascript/ Typescript
                    </div>
                    <div class="badge bg-light text-dark">
                        CSS/SASS
                    </div>
                    <div class="badge bg-light text-dark">
                        Dart
                    </div>
                    <div class="badge bg-light text-dark">
                        Kotlin
                    </div>
                    <div class="badge bg-light text-dark">
                        PHP
                    </div>
                    <div class="badge bg-light text-dark">
                        C#
                    </div>
                    <div class="badge bg-light text-dark">
                        SQL
                    </div>
                    <h5 class="mt-3">Frameworks</h5>
                    <div class="badge bg-light text-dark">
                        React JS
                    </div>
                    <div class="badge bg-light text-dark">
                        Flutter
                    </div>
                    <div class="badge bg-light text-dark">
                        Next JS
                    </div>
                    <div class="badge bg-light text-dark">
                        Vue JS
                    </div>
                    <div class="badge bg-light text-dark">
                        DotNet
                    </div>
                    <div class="badge bg-light text-dark">
                        Tailwind
                    </div>
                    <div class="badge bg-light text-dark">
                        Bootstrap
                    </div>
                </div>
                <h5 class="mb-2 d-flex gap-2 align-items-center">
                    <img width="48" height="48" src="https://img.icons8.com/color/48/whatsapp--v5.png"
                        alt="whatsapp--v5" />
                    <a target="_blank" rel="noopener noreferrer" class="text-white"
                        href="https://api.whatsapp.com/send?phone=6281298698252">+6281298698252</a>
                    <button class="btn btn-sm btn-outline-dark text-light d-flex align-items-center" id="copyBtnWa2"
                        data-text="6281298698252">
                        <span id='copy_wa2' style="font-size: 18px" class="material-symbols-outlined">
                            content_copy
                        </span>
                    </button>
                </h5>
                <h5 class="mb-2 d-flex align-items-center gap-2">
                    <img width="44" height="44" src="https://img.icons8.com/fluency/48/secure-mail.png"
                        alt="secure-mail" />
                    <a target="_blank" rel="noopener noreferrer" class="text-white"
                        href="mailto:adiwardhanawisnu@gmail.com">adiwardhanawisnu@gmail.com</a>

                    <button class="btn btn-sm btn-outline-dark text-light d-flex align-items-center" id="copyBtnEmail2"
                        data-text="adiwardhanawisnu@gmail.com">
                        <span id="copy_email2" style="font-size: 18px" class="material-symbols-outlined">
                            content_copy
                        </span>
                    </button>
                </h5>
                <h5 class="mb-0 mt-3">Address</h5>
                <h5 class="mb-2">Prima Swarga Residence Blok B5 No. 21, Bandung, West Java, Indonesia</h3>
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3959.7741548959116!2d107.60891487499754!3d-7.035809092966137!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zN8KwMDInMDguOSJTIDEwN8KwMzYnNDEuNCJF!5e0!3m2!1sid!2sid!4v1696234236761!5m2!1sid!2sid"
                        width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" class="mb-3"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <b>Education</b>
                    <h5 class="mb-3">
                        <div class="mt-2">Diploma IV (2015 - 2019)</div>
                        <div>
                            State Polytechnic of Malang
                        </div>
                        <div style="font-size: 14px">Electrical Engineer - Digital Telecommunication Network Program Study
                        </div>
                        <div style="font-size: 14px">GPA / IPK 3.55</div>
                    </h5>
                    <b>Work Experiences</b>
                    <div class="mb-3">
                        <h5 class="mb-0 mt-2">PT. Global Service Indonesia</h5>
                        <div class="mb-2">IT Developer</div>
                        <h5 class="mb-0">PT. Distributor Indonesia Unggul</h5>
                        <div>Frontend Developer</div>
                    </div>
                    <b class="mb-2">List Portofolio</b>
                    @if (count($data) > 0)
                        <table class="table table-stripped table-dark mt-2" style="overflow-y: scroll">
                            {{-- <tr>
                                <th class="col-10"></th>
                                @if (!Auth::guest())
                                    <th class="col-1"></th>
                                    <th class="col-1"></th>
                                @endif
                            </tr> --}}
                            @foreach ($data as $item)
                                <tr>
                                    <td>
                                        <a class="text-white text-decoration-none" href="/post/{{ $item->id }}">
                                            {{ $item->folio_name }}
                                        </a>
                                        <div class="d-flex gap-1 justify-content-start align-self-end flex-wrap mt-2">
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
                        <div>You dont have any portofolio yet, <a href="/post/create" class="btn btn-primary">create
                                one</a>
                        </div>
                    @endif
            </div>

            {{-- DESKTOP MODE --}}

            <div style="border-right: 1px solid white; padding-right: 50px" class="d-none d-lg-block col-6">
                <div class="row col-md-12 mb-3">
                    <div class="col-md-6">
                        <div class="position-relative">
                            <img src="https://i.ibb.co/PND5cR4/new-profil.png" style="border-radius: 4px;" class="w-100"
                                alt="">
                            <h2 style="background-color: green; color: white; position: absolute; bottom: 0; right: 0; margin-right: 6px; margin-bottom: 6px; font-size: 18px"
                                class="badge position-absolute">
                                #OPENTOWORK
                            </h2>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-0">Name</h5>
                        <h3 class="mb-2"><u>Wisnu Adi Wardhana</u></h3>
                        <h5 class="mb-0">Age</h5>
                        <h3 class="mb-2"><u>26 y/o</u></h3>
                        <h5 class="mb-0">Work status</h5>
                        <h3 class="mb-2">
                            <u>Employeed</u>
                        </h3>
                        <h5 class="mb-0">Martial status</h5>
                        <h3 class="mb-2">
                            <u>Married</u>
                        </h3>
                        <h5 class="mb-0">Nationality</h5>
                        <h3 class="mb-2">
                            <u>Indonesia</u>
                        </h3>
                    </div>
                </div>
                <div class="mb-3">
                    <h5>Programming Languages</h5>
                    <div class="badge bg-light text-dark">
                        Javascript/ Typescript
                    </div>
                    <div class="badge bg-light text-dark">
                        CSS/SASS
                    </div>
                    <div class="badge bg-light text-dark">
                        Dart
                    </div>
                    <div class="badge bg-light text-dark">
                        Kotlin
                    </div>
                    <div class="badge bg-light text-dark">
                        PHP
                    </div>
                    <div class="badge bg-light text-dark">
                        C#
                    </div>
                    <div class="badge bg-light text-dark">
                        SQL
                    </div>
                    <h5 class="mt-3">Frameworks</h5>
                    <div class="badge bg-light text-dark">
                        React JS
                    </div>
                    <div class="badge bg-light text-dark">
                        Flutter
                    </div>
                    <div class="badge bg-light text-dark">
                        Next JS
                    </div>
                    <div class="badge bg-light text-dark">
                        Vue JS
                    </div>
                    <div class="badge bg-light text-dark">
                        DotNet
                    </div>
                    <div class="badge bg-light text-dark">
                        Tailwind
                    </div>
                    <div class="badge bg-light text-dark">
                        Bootstrap
                    </div>
                </div>
                <h5 class="mb-2 d-flex gap-2 align-items-center">
                    <img width="48" height="48" src="https://img.icons8.com/color/48/whatsapp--v5.png"
                        alt="whatsapp--v5" />
                    <a target="_blank" rel="noopener noreferrer" class="text-white"
                        href="https://api.whatsapp.com/send?phone=6281298698252">+6281298698252</a>
                    <button class="btn btn-sm btn-outline-dark text-light d-flex align-items-center" id="copyBtnWa"
                        data-text="6281298698252">
                        <span id='copy_wa' style="font-size: 18px" class="material-symbols-outlined">
                            content_copy
                        </span>
                    </button>
                </h5>
                <h5 class="mb-2 d-flex align-items-center gap-2">
                    <img width="44" height="44" src="https://img.icons8.com/fluency/48/secure-mail.png"
                        alt="secure-mail" />
                    <a target="_blank" rel="noopener noreferrer" class="text-white"
                        href="mailto:adiwardhanawisnu@gmail.com">adiwardhanawisnu@gmail.com</a>

                    <button class="btn btn-sm btn-outline-dark text-light d-flex align-items-center" id="copyBtnEmail"
                        data-text="adiwardhanawisnu@gmail.com">
                        <span id="copy_email" style="font-size: 18px" class="material-symbols-outlined">
                            content_copy
                        </span>
                    </button>
                </h5>
                <h5 class="mb-2">
                    <img width="48" height="48" src="https://img.icons8.com/color/48/linkedin.png"
                        alt="linkedin" />
                    <a target="_blank" rel="noopener noreferrer" class="text-white"
                        href="https://www.linkedin.com/in/wisnu-adi-wardhana-560473163">https://www.linkedin.com/in/wisnu-adi-wardhana-560473163
                    </a>
                </h5>
                <h5 class="mb-2">
                    <img width="48" height="48" src="https://img.icons8.com/color/48/github--v1.png"
                        alt="github--v1" />

                    <a target="_blank" rel="noopener noreferrer" class="text-white"
                        href="https://github.com/wisnuciwun">https://github.com/wisnuciwun
                    </a>
                </h5>
                <h5 class="mb-2">
                    <img width="48" height="48" src="https://cdn.worldvectorlogo.com/logos/hackerrank.svg"
                        alt="hackerrank--v1" />
                    <a target="_blank" rel="noopener noreferrer" class="text-white"
                        href="https://www.hackerrank.com/adiwardhanawisnu">https://www.hackerrank.com/adiwardhanawisnu
                    </a>

                </h5>
                <h5 class="mb-2">
                    <img width="39" height="39" style="filter: invert(100%); margin-right: 8px"
                        src="https://www.svgrepo.com/show/306018/exercism.svg" alt="exercism--v1" />
                    <a target="_blank" rel="noopener noreferrer" class="text-white"
                        href="https://exercism.org/profiles/wisnuciwun">https://exercism.org/profiles/wisnuciwun
                    </a>
                </h5>
                <h5 class="mb-2">
                    <img width="39" height="39" style="filter: invert(100%); margin-right: 8px"
                        src="https://www.svgrepo.com/show/341985/leetcode.svg" alt="leetcode--v1" />
                    <a target="_blank" rel="noopener noreferrer" class="text-white"
                        href="https://leetcode.com/adiwardhanawisnu">https://leetcode.com/adiwardhanawisnu
                    </a>
                </h5>
                <h5 class="mb-1 mt-3">Address</h5>
                <h5 class="mb-2">Prima Swarga Residence Blok B5 No. 21, Bandung, West Java, Indonesia</h3>
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3959.7741548959116!2d107.60891487499754!3d-7.035809092966137!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zN8KwMDInMDguOSJTIDEwN8KwMzYnNDEuNCJF!5e0!3m2!1sid!2sid!4v1696234236761!5m2!1sid!2sid"
                        width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <div class="d-none d-lg-block col-6" style="padding-left: 50px">
                <b>Education</b>
                <h5 class="mb-3">
                    <div class="mt-2"> State Polytechnic of Malang (2015 - 2019) : Diploma IV</div>
                    <div style="font-size: 14px">Electrical Engineer - Digital Telecommunication Network Program Study
                    </div>
                    <div style="font-size: 14px">GPA / IPK 3.55</div>
                </h5>
                <b>Work Experiences</b>
                <div class="mb-3">
                    <h5 class="mb-0 mt-2">PT. Global Service Indonesia (2019-2022)</h5>
                    <div class="mb-2">IT Developer</div>
                    <h5 class="mb-0">PT. Distributor Indonesia Unggul (2022-Present)</h5>
                    <div>Frontend Developer</div>
                </div>

                <b class="mb-2">List Portofolio</b>
                @if (count($data) > 0)
                    <table class="table table-stripped table-dark mt-2" style="overflow-y: scroll">
                        {{-- <tr>
                            <th class="col-10">Portofolio Name</th>
                            @if (!Auth::guest())
                                <th class="col-1"></th>
                                <th class="col-1"></th>
                            @endif
                        </tr> --}}
                        @foreach ($data as $item)
                            <tr>
                                <td>
                                    <a class="text-white text-decoration-none" href="/post/{{ $item->id }}">
                                        {{ $item->folio_name }}
                                    </a>
                                    <div class="d-flex gap-1 justify-content-start align-self-end flex-wrap mt-2">
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

            {{-- DESKTOP MODE --}}

        </div>
    </section>
    <script>
        const copyBtnWa = document.querySelector('#copyBtnWa');
        const copyBtnEmail = document.querySelector('#copyBtnEmail');
        const copyBtnWa2 = document.querySelector('#copyBtnWa2');
        const copyBtnEmail2 = document.querySelector('#copyBtnEmail2');
        const copyWa = document.querySelector('#copy_wa')
        const copyEmail = document.querySelector('#copy_email')
        const copyWa2 = document.querySelector('#copy_wa2')
        const copyEmail2 = document.querySelector('#copy_email2')

        copyBtnWa.addEventListener('click', e => {
            const input = document.createElement('input');
            input.value = copyBtnWa.dataset.text;
            document.body.appendChild(input);
            input.select();
            if (document.execCommand('copy')) {
                copyWa.classList.remove('material-symbols-outlined');
                document.body.removeChild(input);
                copyWa.textContent = 'copied!'
                copyWa.class = ''
            }
        });

        copyBtnEmail.addEventListener('click', e => {
            const input = document.createElement('input');
            input.value = copyBtnEmail.dataset.text;
            document.body.appendChild(input);
            input.select();
            if (document.execCommand('copy')) {
                copyEmail.classList.remove('material-symbols-outlined');
                document.body.removeChild(input);
                copyEmail.textContent = 'copied!'
            }
        });

        copyBtnWa2.addEventListener('click', e => {
            const input = document.createElement('input');
            input.value = copyBtnWa2.dataset.text;
            document.body.appendChild(input);
            input.select();
            if (document.execCommand('copy')) {
                copyWa2.classList.remove('material-symbols-outlined');
                document.body.removeChild(input);
                copyWa2.textContent = 'copied!'
                copyWa2.class = ''
            }
        });

        copyBtnEmail2.addEventListener('click', e => {
            const input = document.createElement('input');
            input.value = copyBtnEmail2.dataset.text;
            document.body.appendChild(input);
            input.select();
            if (document.execCommand('copy')) {
                copyEmai2.classList.remove('material-symbols-outlined');
                document.body.removeChild(input);
                copyEmail2.textContent = 'copied!'
            }
        });
    </script>
@endsection
