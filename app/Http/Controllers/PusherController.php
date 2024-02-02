<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\UserStatistic;
use App\Events\PusherBroadcast;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Foundation\Application;

class PusherController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function createRoom(Request $request)
    {
        // Get authenticated user's ID
        $ownerId = Auth::id();
        
        $tags = explode(',', $request->input('createTags'));

        // Update the favorite tags in UserStatistic
        $this->updateFavouriteTags($ownerId, $tags);

        // Generate a random ASCII number for the chat room name
        $randomNumber = Str::random(8);

        // Create a new chat room
        $chatRoom = ChatRoom::create([
            'name' => $randomNumber,
            'tags' => json_encode($tags),
            'owner_id' => $ownerId  // Set the owner_id here
        ]);

        // Get the room name using str_slug
        $roomName = Str::slug($chatRoom->name);

        // Redirect to the chat room route with the generated roomName
        return redirect()->route('chatRoom', ['roomName' => $roomName]);
    }

    public function joinRoom(Request $request)
    {
        $tags = explode(',', $request->input('joinTags'));
        $personalityPreference = $request->input('personalityPreference');

        // Update the favorite tags in UserStatistic
        $this->updateFavouriteTags(Auth::id(), $tags);

        // Fetch chat rooms that have at least one matching tag
        $chatRooms = ChatRoom::where(function ($query) use ($tags, $personalityPreference) {
            foreach ($tags as $tag) {
                $query->orWhereJsonContains('tags', $tag);
            }

            if ($personalityPreference === 'alike') {
                // Add a condition to filter by personality preference (People Alike)
                $query->whereHas('owner.characteristics', function ($ownerQuery) {
                    $ownerQuery->where('personality', auth()->user()->characteristics->personality);
                });
            } elseif ($personalityPreference === 'differ') {
                // Add a condition to filter by personality preference (Differ Pairing)
                $query->whereHas('owner.characteristics', function ($ownerQuery) {
                    $ownerQuery->where('personality', '!=', auth()->user()->characteristics->personality);
                });
            }
        })->orderBy('name')->get();

        // Return the view with search results
        return view('join_room', ['chatRooms' => $chatRooms]);
    }

    // Helper method to update favorite tags in UserStatistic
    private function updateFavouriteTags($userId, $tags)
    {
        $userStat = UserStatistic::firstOrNew(['user_id' => $userId]);

        $favouriteTags = json_decode($userStat->favourite_tags, true) ?: [];

        // Update or increment the count for each tag
        foreach ($tags as $tag) {
            $favouriteTags[$tag] = isset($favouriteTags[$tag]) ? $favouriteTags[$tag] + 1 : 1;
        }

        // Sort tags based on usage count in descending order
        arsort($favouriteTags);

        // Take the top N tags, you can adjust the number as needed
        $topTags = array_slice($favouriteTags, 0, 15, true);

        // Save the updated favorite tags
        $userStat->favourite_tags = json_encode($topTags);
        $userStat->save();
    }

    public function chatRoom($roomName)
    {
        // Retrieve the chat room by room name
        $chatRoom = ChatRoom::where('name', 'like', $roomName . '%')->first();

        // Display the chat room view
        return view('chat_room', ['chatRoom' => $chatRoom]);
    }

    /**
     * Leave the chat room without deleting it.
     *
     * @param  ChatRoom  $chatRoom
     * @return \Illuminate\Http\RedirectResponse
     */

    public function deleteRoom($roomName)
    {
        $user = Auth::user();
      
        // Check if the authenticated user is the owner of the chat room
        $chatRoom = ChatRoom::where('name', 'like', $roomName . '%')->first();
         
        // Check if the user is the owner and also if the chat room exists
        if ($chatRoom && $user->id === $chatRoom->owner_id) {
            $chatRoom->delete();
            return redirect()->route('index')->with('success', 'Chat room deleted successfully.');
        }
     
        return redirect()->route('index')->with('error', 'You are not authorized to delete this chat room.');
    }
     
    public function leaveRoom()
    {
        return redirect()->route('index')->with('success', 'You have left the chat room.');
    }

    public function broadcast(Request $request)
    {
        broadcast(new PusherBroadcast($request->get('message')))->toOthers();

        return view('broadcast', ['message' => $request->get('message')]);
    }

    public function receive(Request $request)
    {
        return view('receive', ['message' => $request->get('message')]);
    }

    public function updateUserStatistics(Request $request)
    {
        $userId = Auth::id();

        // Store the sent message in the database
        $message = new Message([
            'user_id' => $userId,
            'content' => $request->get('message'),
        ]);

        $message->save();

        // Update total_messages for every message
        $userStat = UserStatistic::firstOrNew(['user_id' => $userId]);
        $userStat->total_messages += 1;

        // Update totalChatTime based on the time since the last message
        if ($userStat->messages->count() > 1) {
            // Get the latest and earliest messages
            $latestMessage = $userStat->messages->max('created_at');
            $earliestMessage = $userStat->messages->min('created_at');

            // Calculate the time difference in minutes
            $timeDifference = Carbon::parse($latestMessage)->diffInMinutes(Carbon::parse($earliestMessage));

            $userStat->total_chat_time += $timeDifference;
        }

        // Save the updated total messages and total chat time
        $userStat->save();

        // Optionally, delete messages that are not in the latest month to avoid too many data
        $userStat->messages()
            ->where('created_at', '<', now()->subMonth())
            ->delete();

        return response()->json(['success' => true]);
    }
}
