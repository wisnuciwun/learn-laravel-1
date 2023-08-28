{{-- to see the result in /layouts/app --}}
@extends('layouts.app')
{{-- @extends('layouts/app') --}}

@section('content')
    <div>
        <div style="display: flex; justify-content: center; align-items: center; width: 100vw; height: 95vh; flex-wrap:wrap"
            class="mt-12">
            <div style="text-align: center">
                <h1 style="color: black; font-size: 30px; font-weight: bold">
                    Welcome to my first <span style="color: red">Laravel</span> Website
                </h1>
                <h3>{{ $data['subTitle'] }}</h3>
                <div style="text-align: left">
                    <br>
                    @if (count($data['listPortofolio']) > 0)
                        <ul>
                            @foreach ($data['listPortofolio'] as $portofolio)
                                <li>{{ $portofolio }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

        </div>
        <div style="display: flex; justify-content: center;">
            <h4><?php echo $connect; ?></h4>
            <h4>
                <a style="text-decoration: none" href={{ $linkedinUrl }} target="_blank">&nbsp;Wisnu Adi Wardhana</a>
            </h4>
        </div>
    </div>
@endsection
