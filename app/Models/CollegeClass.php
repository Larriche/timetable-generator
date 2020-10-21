<?php

namespace App\Models;

class CollegeClass extends Model
{
    /**
     * The DB table used by this model
     *
     * @var string
     */
    protected $table = 'classes';

    protected $guarded = ['id'];

    protected $relations = [];

    /**
     * Get the rooms that are not available to this class
     */
    public function unavailable_rooms()
    {
        return $this->belongsToMany(Room::class, 'unavailable_rooms', 'class_id', 'room_id');
    }

    /**
     * The building block this class can have lectures at
     */
    public function block() {
        return $this->belongsTo(Block::class, 'block_id');
    }

    public function courses()
    {
        return $this->hasMany(ClassCourse::class, 'class_id');
    }

    /**
     * Get classes with no courses set up for them
     */
    public function scopeHavingNoCourses($query)
    {
        return $query->has('courses', '<', 1);
    }
}
