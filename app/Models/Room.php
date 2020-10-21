<?php

namespace App\Models;

class Room extends Model
{
    /**
     * DB table this model uses
     *
     * @var string
     */
    protected $table = 'rooms';

    /**
     * Fields to be protected from mass assignment
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Block for this room
     *
     * @return \Illuminate\Database\Collection
     */
    public function block()
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    /**
     * Rooms near this room
     *
     * @return \Illuminate\Database\Collection
     */
    public function adjacent_rooms()
    {
        return $this->belongsToMany(Room::class, 'adjacent_rooms', 'room_id', 'adjacent_room_id');
    }
}
