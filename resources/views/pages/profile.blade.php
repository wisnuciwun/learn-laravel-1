@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-start">
        <h2 class="w-100">
            Profile
        </h2>
        <div class="col-6">
            <div>Name</div>
            <div class="mb-2">Wisnu</div>
            <div>Age</div>
            <div class="mb-2">26</div>
            <div>Education</div>
            <div class="mb-2">Diploma IV</div>
            <div>Last status</div>
            <div class="mb-2">Employeed (open to work)</div>
        </div>
        <div class="col-6">
            @if (count($data) > 0)
                <table class="table table-stripped">
                    <tr>
                        <th class="col-10">Portofolio Name</th>
                        <th class="col-1"></th>
                        <th class="col-1"></th>
                    </tr>

                    @foreach ($data as $item)
                        <tr>
                            <td>{{ $item->folio_name }}</td>
                            <td><a class="btn btn-primary" href="/posts/{{ $item->id }}/edit">Edit</a></td>
                            <td>
                                {{ Form::open(['route', 'post.destroy', $item->id, 'class' => 'btn btn-danger', 'method' => 'POST']) }}
                                {{ Form::hidden('_method', 'DELETE') }}
                                {{ Form::submit('Delete', ['class' => 'btn btn-danger']) }}
                            </td>
                            {{-- <td><a class="btn btn-danger" href="/posts/{{ $item->id }}/edit">Delete</a></td> --}}
                        </tr>
                    @endforeach
                </table>
            @else
                <div>You dont have any portofolio yet, <a href="/post/create" class="btn btn-primary">create one</a></div>
            @endif
        </div>
    </div>
@endsection
