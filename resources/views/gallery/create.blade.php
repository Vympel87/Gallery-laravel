@extends('auth.layouts')

@section('content')

    <form action="{{ route('gallery.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3 row">
            <label for="title" class="col-md-4 col-form-label text-md-end text-start">Title</label>
            <div class="col-md-6">
                <input type="text" class="form-control" id="title" name="title">
                @if ($errors->has('title'))
                    <span class="text-danger">{{ $errors->first('title') }}</span>
                @endif
            </div>
        </div>
        <div class="mb-3 row">
            <label for="description" class="col-md-4 col-form-label text-md-end text-start">Description</label>
            <div class="col-md-6">
                <textarea class="form-control" id="description" rows="5" name="description"></textarea>
                @if ($errors->has('description'))
                    <span class="text-danger">{{ $errors->first('description') }}</span>
                @endif
            </div>
        </div>
        <div class="mb-5">
            <label for="picture" class="form-label">Picture</label>
            <input type="file" class="form-control" id="" name="picture" value="">
            @if ($errors->has('picture'))
                <span class="text-danger">{{ $errors->first('picture') }}</span>
            @endif
        </div>
        <div class="d-flex flex-column w-100 justify-content-evenly mt-4 align-items-center">
            <button type="submit" class="btn btn-outline-primary">Create</button>
            <a href="/gallery" class="btn btn-outline-danger text-center my-3">
                Back
            </a>
        </div>
    </form>

@endsection