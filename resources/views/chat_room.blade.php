<!DOCTYPE html>
<html lang="en">
<head>
  <title>CloakChat</title>
  <link rel="icon" href="https://assets.edlin.app/favicon/favicon.ico"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- JavaScript -->
  <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
  <!-- End JavaScript -->

  <!-- CSS -->
  <link rel="stylesheet" href="/style.css">
  <!-- End CSS -->

</head>

<body>
<div class="chat">

  <!-- Header -->
  <div class="top">
    <h3>Room: {{ $chatRoom->name }}</h3>
    @if($chatRoom->tags)
        <p>
            Tags:
            @foreach(explode(',', $chatRoom->tags) as $tag)
                {{ trim($tag) }}
            @endforeach
        </p>
    @endif
  </div>

  <div>
    <!-- Leave and Delete Room Button for owners -->
    @if($chatRoom && Auth::id() === $chatRoom->owner_id)
      <a href="{{ route('deleteRoom', $chatRoom->name) }}" class="btn btn-danger">Leave and Delete Room</a>
    @elseif($chatRoom)
      <!-- Leave Room Button for non-owners -->
      <a href="{{ route('leaveRoom') }}" class="btn btn-danger">Leave Room</a>
    @endif
  <!-- End Header -->

  <!-- Chat -->
  <div class="messages">
    @include('receive', ['message' => "Hey! What's up!  👋"])
    @include('receive', ['message' => "Wait for others to join this room and you can chat with them!"])
  </div>
  <!-- End Chat -->

  <!-- Footer -->
  <div class="bottom">
    <form>
      <input type="text" id="message" name="message" placeholder="Enter message..." autocomplete="off">
      <button type="submit"></button>
    </form>
  </div>
  <!-- End Footer -->

</div>
</body>

<script>
  const pusher  = new Pusher('{{config('broadcasting.connections.pusher.key')}}', {cluster: 'ap1'});
  const channel = pusher.subscribe('public');

  //Receive messages
  channel.bind('chat', function (data) {
    $.post("/chat/chat-room/{{ $chatRoom->name }}/receive", {
      _token:  '{{csrf_token()}}',
      message: data.message,
    })
     .done(function (res) {
       $(".messages > .message").last().after(res);
       $(document).scrollTop($(document).height());
     });
  });

  //Broadcast messages
  $("form").submit(function (event) {
    event.preventDefault();

    // Get the message content from the form
    const messageContent = $("form #message").val();

    // Send data to the server for user statistics update
    updateUserStatistics(messageContent);

    $.ajax({
      url:     "/chat/chat-room/{{ $chatRoom->name }}/broadcast",
      method:  'POST',
      headers: {
        'X-Socket-Id': pusher.connection.socket_id
      },
      data:    {
        _token:  '{{csrf_token()}}',
        message: $("form #message").val(),
      }
    }).done(function (res) {
      $(".messages > .message").last().after(res);
      $("form #message").val('');
      $(document).scrollTop($(document).height());
    });
  });

  // Function to update user statistics on the server
  function updateUserStatistics(messageContent) {
    $.ajax({
      url: "/updateUserStatistics",
      method: 'POST',
      data: {
        _token: '{{ csrf_token() }}',
        message: messageContent,
      },
      success: function(response) {
        console.log(response);
      },
      error: function(error) {
        console.error(error);
      }
    });
  }
</script>
</html>
