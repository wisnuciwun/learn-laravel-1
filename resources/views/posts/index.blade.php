@extends('layouts.app')

@section('content')
    <h3>Portofolios</h3>
    <div class="d-flex gap-2 flex-wrap">
        @if (count($data) > 0)
            @foreach ($data as $item)
                <div class="card w-25">
                    <img class="card-img-top w-100" src={{ $item->image_url }} alt="Card image cap">
                    <div class="card-body">
                        <div onclick="window.location.replace('/post/{{ $item->id }}')" style="cursor:pointer"
                            class="card-title">
                            {{ $item->folio_name }}</div>
                        <div class="card-text">
                            {{ $item->description }}
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <p>No portofolio found</p>
        @endif
    </div>
    <div class="d-flex mt-3">
        {{-- add inside links() to use any style or bootstrap --}}
        {{ $data->links('pagination::bootstrap-4') }}
    </div>
@endsection
