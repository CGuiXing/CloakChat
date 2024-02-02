<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCharacteristic extends Model
{
    use HasFactory;

    protected $table = 'user_characteristics';
    protected $fillable = [
        'user_id',
        'ei_scale',
        'lie_scale',
        'lying',
        'personality',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
