
@extends('layouts.main')

@section('content')
<div class="container">
    
    <!-- Display chat messages and other content here -->
    <!-- Create Room Form -->
    <form action="{{ route('createRoom') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="createTags">Room Tags:</label>
            <input type="text" class="form-control" id="createTags" name="createTags" data-role="tagsinput" placeholder="Separated tags by Enter">
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>

    <hr>

    <!-- Join Room Form -->
    <form action="{{ route('joinRoom') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="joinTags">Room Tags:</label>
            <input type="text" class="form-control" id="joinTags" name="joinTags" data-role="tagsinput" placeholder="Separated tags by Enter">
        </div>

        <p>Filter Options:</p>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="personalityPreference" id="alike" value="alike" checked>
            <label class="form-check-label" for="alike">
                People Alike
            </label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="radio" name="personalityPreference" id="differ" value="differ">
            <label class="form-check-label" for="differ">
                Differ Pairing
            </label>
        </div>

        <button type="submit" class="btn btn-success">Join</button>
    </form>
</div>
@endsection
