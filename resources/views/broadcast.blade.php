<div class="right message">
  <div class="image">
    <img src="{{ asset('images/user-sign-icon-front-side-with-white-background.jpg') }}" class="img-circle elevation-2" alt="User Image">
    <span class="brand-text font-weight-light" style="color: black;">{{ auth()->user()->name }}</span>
  </div>
    <p>{{$message}}</p>
</div>
  