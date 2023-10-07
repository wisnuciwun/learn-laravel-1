{{-- to see the result in /layouts/app --}}
@extends('layouts.app')
{{-- @extends('layouts/app') --}}

@section('content')
    <div>
        <img src="https://i.ibb.co/s5FRcmX/welcome.gif" style="position: absolute; top: 0; left: 0; z-index: -1; opacity: 0.3"
            class="w-100 h-100" alt="">
        {{-- <div aria-disabled="true" style="padding-top:100.000%;"><iframe src="https://gifer.com/embed/7Ik1" width="100%"
                height="100%" style='position:absolute;top:0;left:0;' frameBorder="0" allowFullScreen></iframe>
        </div> --}}
        <div style="height: 95vh; flex-wrap:wrap" class="mt-12 d-flex justify-content-center align-items-center gap-5">
            <div>
                <div style="text-align: center">
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('index') }}" class="fade-in profile-container mb-2"
                            style="border-radius: 50%; overflow: hidden; height: 150px; width: 150px">
                            <img src="https://i.ibb.co/PND5cR4/new-profil.png" class="img-profile appear" alt="">
                        </a>
                    </div>
                </div>
                <div class="w-100 text-center mb-4 text-white" style="font-size: 10px">
                    [ Click image to visit profile page ]
                </div>
                <div class="w-100 text-center mb-3">
                    <h1 class="fade-in" style="color: black; font-size: 30px; font-weight: bold">
                        Welcome to my <a href="{{ route('post.index') }}" class="btn btn-outline-light">Portofolio</a>
                        website
                    </h1>
                </div>
                <div class="w-100 d-flex justify-content-center">
                    <div class="d-lg-block d-none w-50 text-center text-white">
                        Hi everyone, i am <b style="font-size: 18px">Wisnu</b>. I'm a programmer who focussed as
                        a web front-end
                        developer. Specializing in <b style="font-size: 18px">React.js, Vue.js,
                            Next.js, and Laravel</b>. I'm equally
                        comfortable developing mobile apps with <b style="font-size: 18px">Kotlin</b> and <b
                            style="font-size: 18px">Flutter</b>. I'm known for my fast learning ability
                        and
                        adaptability to different frameworks and languages, which keeps me at the forefront of technology
                        trends.
                    </div>
                    <div class="d-lg-none w-100 text-center text-white">
                        Hi everyone, i am <b style="font-size: 18px">Wisnu</b>. I'm a programmer who focussed as
                        a web front-end
                        developer. Specializing in <b style="font-size: 18px">React.js, Vue.js,
                            Next.js, and Laravel</b>. I'm equally
                        comfortable developing mobile apps with <b style="font-size: 18px">Kotlin</b> and <b
                            style="font-size: 18px">Flutter</b>. I'm known for my fast learning ability
                        and
                        adaptability to different frameworks and languages, which keeps me at the forefront of technology
                        trends.
                    </div>
                </div>

            </div>
        </div>
        <div style="display: flex; justify-content: center;">
            {{-- <h4><?php echo $connect; ?></h4> --}}
        </div>
    </div>
@endsection
