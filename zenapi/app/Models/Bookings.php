<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    public function type()
    {
        return $this->belongsTo('App\Models\RoomTypes', 'roomtype');
    }
}
