<?php

namespace App\Models;

class Block extends Model
{
    /**
     * DB table this model uses
     *
     * @var string
     */
    protected $table = 'blocks';

    /**
     * Fields to be protected from mass assignment
     *
     * @var array
     */
    protected $guarded = ['id'];


    /**
     * Rooms near this room
     *
     * @return \Illuminate\Database\Collection
     */
    public function rooms()
    {
        return $this->hasMany(Room::class, 'block_id');
    }
}
