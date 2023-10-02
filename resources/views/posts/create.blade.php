@extends('layouts.app')

@section('content')
    <h3 class="text-white">Create portofolio</h3>
    {{-- add 'enctype' => 'multipart/data' to use upload file --}}
    {!! Form::open(['route' => 'post.store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
    <div class="form-group mt-3">
        {{-- first folio_name is it's id, and the next is it's value --}}
        {{ Form::label('folio_name', 'Portofolio Name', ['class' => 'text-white']) }}
        {{ Form::text('folio_name', '', ['class' => 'form-control mb-2', 'placeholder' => 'What is your project name?']) }}
    </div>
    <div class="form-group mb-2">
        {{ Form::label('description', 'Description', ['class' => 'text-white']) }}
        {{ Form::textarea('description', '', ['class' => 'form-control text-dark', 'placeholder' => 'Write your project description']) }}
    </div>
    <div class="form-group">
        {{ Form::label('url', 'App Url', ['class' => 'text-white']) }}
        {{ Form::text('url', '', ['class' => 'form-control mb-2', 'placeholder' => 'Add your project url (if exist)']) }}
    </div>
    {{-- <div class="form-group">
        {{ Form::label('image_url', 'App Screenshot Url') }}
        {{ Form::text('image_url', '', ['class' => 'form-control mb-2', 'placeholder' => 'Add your app screenshot url']) }}
    </div> --}}
    <div class="form-group mb-2 text-white">
        {{ Form::label('image_url', 'App Screenshot Url', ['class' => 'text-white']) }}
        @php
            $isUseFile = false;
        @endphp
        {{-- <div class="form-check form-switch mb-2 mt-1">
            <input @if ($isUseFile) checked @endif class="form-check-input" type="checkbox" role="switch"
                id="flexSwitchCheckChecked">
            <label class="form-check-label" for="flexSwitchCheckChecked">use file</label>
        </div> --}}
        {{ $isUseFile }}
        @if ($isUseFile)
            {{ Form::file('image_url') }}
        @else
            {{ Form::text('image_url', '', ['class' => 'form-control mb-2', 'placeholder' => 'Add your app screenshot url']) }}
        @endif
    </div>
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleCheckbox = document.getElementById('flexSwitchCheckChecked');

            toggleCheckbox.addEventListener('change', function() {
                // When the checkbox state changes, update the isUseFile variable
                const isChecked = toggleCheckbox.checked;
                @php
                    
                @endphp
                @if ($isUseFile)
                    @if ($isUseFile == 'teesting')
                        const isUseFile = 'false';
                    @else
                        const isUseFile = 'zzzz';
                    @endif
                @else
                    const isUseFile = isChecked ? 'true' : 'false';
                @endif

                // You can now use the updated isUseFile variable as needed
                console.log(isUseFile);
            });


        });
    </script> --}}
    <div class="form-group">
        {{ Form::label('created_at', 'Created At', ['class' => 'text-white']) }}
        {{ Form::text('created_at', '', ['class' => 'form-control mb-2', 'placeholder' => 'When you create that project?']) }}
    </div>
    <div class="form-group">
        {{ Form::label('short_desc', 'Short Description', ['class' => 'text-white']) }}
        {{ Form::text('short_desc', '', ['class' => 'form-control mb-2', 'placeholder' => 'You will see this in portofolio home']) }}
    </div>
    <div class="form-group">
        {{ Form::label('hashtags', 'Hashtags', ['class' => 'text-white']) }}
        {{ Form::text('hashtags', '', ['class' => 'form-control mb-2', 'placeholder' => 'Tags for your project']) }}
    </div>
    {{-- <textarea name="content" id="editor"></textarea> --}}
    <script>
        ClassicEditor
            .create(document.querySelector('#description'))
            .catch(error => {
                console.error(error);
            });
    </script>

    {{ Form::submit('Submit', ['class' => 'btn btn-outline-light text-white mt-4']) }}
    {!! Form::close() !!}
@endsection
