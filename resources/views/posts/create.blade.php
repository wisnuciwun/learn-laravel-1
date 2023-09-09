@extends('layouts.app')

@section('content')
    <h3>Create portofolio</h3>
    {{-- add 'enctype' => 'multipart/data' to use upload file --}}
    {!! Form::open(['route' => 'post.store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
    <div class="form-group mt-3">
        {{-- first folio_name is it's id, and the next is it's value --}}
        {{ Form::label('folio_name', 'Portofolio Name') }}
        {{ Form::text('folio_name', '', ['class' => 'form-control mb-2', 'placeholder' => 'What is your app name?']) }}
    </div>
    <div class="form-group mb-2">
        {{ Form::label('description', 'Description') }}
        {{ Form::textarea('description', '', ['class' => 'form-control', 'placeholder' => 'Write your app description']) }}
    </div>
    <div class="form-group">
        {{ Form::label('url', 'App Url') }}
        {{ Form::text('url', '', ['class' => 'form-control mb-2', 'placeholder' => 'Add your app url (if exist)']) }}
    </div>
    {{-- <div class="form-group">
        {{ Form::label('image_url', 'App Screenshot Url') }}
        {{ Form::text('image_url', '', ['class' => 'form-control mb-2', 'placeholder' => 'Add your app screenshot url']) }}
    </div> --}}
    <div class="form-group mb-2">
        {{ Form::label('image_url', 'App Screenshot Url') }}
        <br>
        {{ Form::file('image_url') }}
    </div>
    <div class="form-group">
        {{ Form::label('created_at', 'Created At') }}
        {{ Form::text('created_at', '', ['class' => 'form-control mb-2', 'placeholder' => 'When you create that app?']) }}
    </div>
    {{-- <textarea name="content" id="editor"></textarea> --}}
    <script>
        ClassicEditor
            .create(document.querySelector('#description'))
            .catch(error => {
                console.error(error);
            });
    </script>

    {{ Form::submit('Submit', ['class' => 'btn btn-secondary mt-4']) }}
    {!! Form::close() !!}
@endsection
