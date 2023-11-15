@extends('auth.layouts')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">User Table</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Image</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userss as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <img src="{{asset('storage/photos/'.$user->photo )}}" width="150px">
                            </td>
                            <td>
                                <form action="{{ route('user.destroy', $user->id) }}" method="post">
                                    @csrf
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin untuk dihapus?')">Hapus</button>
                                 </form>
                                 <form action="{{ route('user.update', $user->id) }}" method="post">
                                   @csrf
                                   <a class="btn btn-primary mt-2" href="{{ route('user.update', $user->id) }}" role="button">Update</a>
                                 </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection