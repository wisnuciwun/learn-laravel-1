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
                        <div onclick="window.location.replace('/post')" class="fade-in profile-container mb-2"
                            style="border-radius: 50%; overflow: hidden; height: 150px; width: 150px">
                            <img src="https://i.ibb.co/PND5cR4/new-profil.png" class="img-profile appear" alt="">
                        </div>
                    </div>
                </div>
                <div class="w-100 text-center mb-4" style="font-size: 10px">
                    [ Click image to know me more ]
                </div>
                <div>
                    <h1 class="fade-in" style="color: black; font-size: 30px; font-weight: bold">
                        Welcome to my <button onclick="window.location.replace('/post')"
                            class="btn btn-outline-light">Portofolio</button>
                        website
                    </h1>
                </div>
            </div>
        </div>
        <div style="display: flex; justify-content: center;">
            {{-- <h4><?php echo $connect; ?></h4> --}}
        </div>
    </div>
@endsection
