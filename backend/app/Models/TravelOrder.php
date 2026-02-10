<?php

namespace App\Models;

use App\Enums\TravelOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'requester_name',
        'destination',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'status' => TravelOrderStatus::class,
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}