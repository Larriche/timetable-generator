<?php

namespace App\Services;

use App\Models\Room;

class RoomsService extends AbstractService
{
    /*
     * The model to be used by this service.
     *
     * @var \App\Models\Room
     */
    protected $model = Room::class;

    /**
     * Show resources with their relations.
     *
     * @var bool
     */
    protected $showWithRelations = true;

    /**
     * Save a new room in the database
     *
     * @param $data Data for creating a room
     * @return App\Models\Room $room Created room
     */
    public function store($data = [])
    {
        $room = parent::store($data);

        if ($room) {
            $room->adjacent_rooms()->sync(array_get($data, 'adjacent_room_ids', []));
        }

        return $room;
    }

    /**
     * Get a given room
     *
     * @param int $id Id of room
     */
    public function show($id)
    {
        $room = parent::show($id);
        $room->adjacent_room_ids = $room->adjacent_rooms()->pluck('rooms.id');

        return $room;
    }

    /**
     * Update a room
     *
     * @param int $id Id of room we are updating
     * @param array $data Data for update
     */
    public function update($id, $data = [])
    {
        $room = parent::update($id, $data);

        if ($room) {
            $room->adjacent_rooms()->sync(array_get($data, 'adjacent_room_ids', []));
        }

        return $room;
    }
}