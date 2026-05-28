<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class EmergencyContact extends Model
{
    use Notifiable;

    protected $fillable = [
        'user_id',
        'name',
        'phone_number',
        'relationship',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
