<!-- resources/views/test/index.blade.php -->

@extends('layouts.main')

@section('content') 
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title">Extroversion / Introversion Test</h3>
          </div>
          <!-- /.card-header -->
            <div class="card-body">
                <form action="/test/submit" method="post">
                    @csrf
            
                    @foreach($questions as $key => $question)
                        <label>{{ $question }} </label>
                        <select class="custom-select" name="q{{ $key + 1 }}">
                            <option value="" selected disabled>Select an option</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select><br><br>
                    @endforeach
            
                    <div class="card-footer">
                        <button type="submit" class="btn btn-block btn-success" id="submitButton">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

