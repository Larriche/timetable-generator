<?php

namespace App\Services;

use App\Models\Block;

class BlocksService extends AbstractService
{
    /**
     * The model to be used by this service.
     *
     * @var App\Models\Block
     */
    protected $model = Block::class;

    /**
     * Show resources with their relations.
     *
     * @var bool
     */
    protected $showWithRelations = true;

    /**
     * Store a new block in the DB
     *
     * @param array $data Data for creating block
     */
    public function store($data = [])
    {
        $block = Block::create($data);

        return $block;
    }

    /**
     * Get the block with the given id
     *
     * @param int $id The Id of the building block
     */
    public function show($id)
    {
        $block = Block::find($id);

        return $block;
    }

    /**
     * Update the block with the given id
     * with new data
     *
     * @param int $id The id of the block
     * @param array $data Data for update
     */
    public function update($id, $data = [])
    {
        $block = Block::find($id);

        if (!$block) {
            return null;
        }

        $block->update($data);

        return $block;
    }
}