<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'content'];

    public function userStatistic()
    {
        return $this->belongsTo(UserStatistic::class, 'user_statistic_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getPrevious()
    {
        return self::where('id', '<', $this->id)
            ->orderBy('id', 'desc')
            ->first();
    }
}
