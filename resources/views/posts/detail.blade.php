@extends('layouts.app')

@section('content')
    <h3>detail</h3>
    <div class="card w-100">
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
    <button onclick="window.location.replace('/post')" class="btn btn-secondary mt-2">Back</button>
@endsection
