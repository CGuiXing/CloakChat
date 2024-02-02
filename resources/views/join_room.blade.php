@extends('layouts.main')

@section('content')
    <h1>Chat Room Search</h1>

    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-end">
                    <a href="{{ url('/chat') }}" class="btn btn-sm btn-danger">Back</a>
                </div>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Room</th>
                        <th>Tags</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($chatRooms as $room)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $room->name }}</td>
                            <td>{{ $room->tags }}</td>
                            <td>
                                <form action="{{ route('chatRoom', ['roomName' => $room->name]) }}" method="GET" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Join Room</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No matching chat rooms found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
