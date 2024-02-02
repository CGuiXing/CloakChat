<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\UserStatistic;

class DashboardController extends Controller
{
    public function index()
    {
        // Get the authenticated user's ID
        $userId = auth()->id();

        // Get user statistics
        $userStat = UserStatistic::where('user_id', $userId)->first();

        // Check if $userStat is not null
        if ($userStat) {
            // Get total messages and total chat time for all time
            $totalMessages = $userStat->messages()->count();

            $firstMessage = $userStat->messages->min('created_at');
            $lastMessage = $userStat->messages->max('created_at');
            $totalChatTime = $firstMessage->diffInMinutes($lastMessage);


            // Calculate total messages and total chat time for the latest month
            $latestMonthStartDate = Carbon::now()->startOfMonth();
            $latestMonthEndDate = Carbon::now()->endOfMonth();

            $totalMessagesLatestMonth = $userStat->messages()
                ->whereBetween('created_at', [$latestMonthStartDate, $latestMonthEndDate])
                ->count();

            // Calculate total chat time for the latest month
            // Calculate total chat time for the latest month
            $messagesLatestMonth = $userStat->messages()
                ->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->get();

            $firstMessageLatestMonth = $messagesLatestMonth->min('created_at');
            $lastMessageLatestMonth = $messagesLatestMonth->max('created_at');
            $totalChatTimeLatestMonth = $firstMessageLatestMonth->diffInMinutes($lastMessageLatestMonth);

            // Calculate total messages by weeks for the latest month
            $totalMessagesByWeeks = $userStat->messages()
                ->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->get()
                ->groupBy(function ($message) {
                    return Carbon::parse($message->created_at)->weekOfMonth;
                })
                ->map
                ->count();

            // Get top 5 favourite tags
            $topFavouriteTags = json_decode($userStat->favourite_tags, true) ?: [];

            // Pass the data to the view
            return view('test.dashboard', [
                'totalMessages' => $totalMessages,
                'totalChatTime' => $totalChatTime,
                'totalMessagesLatestMonth' => $totalMessagesLatestMonth,
                'totalChatTimeLatestMonth' => $totalChatTimeLatestMonth,
                'totalMessagesByWeeks' => $totalMessagesByWeeks,
                'topFavouriteTags' => $topFavouriteTags,
            ]);
        } else {
            // Handle the case when $userStat is null (user statistics not found)
            return view('test.dashboard', [
                'totalMessages' => 0,
                'totalChatTime' => 0,
                'totalMessagesLatestMonth' => 0,
                'totalChatTimeLatestMonth' => 0,
                'totalMessagesByWeeks' => [],
                'topFavouriteTags' => [],
            ]);
        }
    }
}
