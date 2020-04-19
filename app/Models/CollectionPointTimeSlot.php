<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionPointTimeSlot extends Model
{
    protected $table = 'collection_point_time_slots';

    protected $types = [
        'user_pickup'    => 'User Pickup',
        'charity_pickup' => 'Charity Pickup',
    ];

    protected $fillable = [
        'collection_point_id',
        'start_time',
        'end_time',
        'max_capacity',
        'type',
    ];

    protected $casts = [
        'collection_point_id' => 'integer',
        'max_capacity'        => 'integer',
    ];

    public function collectionPoint()
    {
        return $this->belongsTo(CollectionPoint::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
