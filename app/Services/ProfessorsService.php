<?php

namespace App\Services;

use App\Models\Professor;

class ProfessorsService extends AbstractService
{
    /*
     * The model to be used by this service.
     *
     * @var \App\Models\Professor
     */
    protected $model = Professor::class;

    /**
     * Show resources with their relations.
     *
     * @var bool
     */
    protected $showWithRelations = true;

    /**
     * Store a new professor in the DB
     *
     * @param array $data Data for creating professor
     */
    public function store($data = [])
    {
        $professor = Professor::create([
            'name' => $data['name'],
            'email' => $data['email']
        ]);

        return $professor;
    }

    /**
     * Get the professor with the given id
     *
     * @param int $id The Id of the professor
     */
    public function show($id)
    {
        $professor = Professor::find($id);

        return $professor;
    }

    /**
     * Update the professor with the given id
     * with new data
     *
     * @param int $id The id of the professor
     * @param array $data Data for update
     */
    public function update($id, $data = [])
    {
        $professor = Professor::find($id);

        if (!$professor) {
            return null;
        }

        $professor->update([
            'name' => $data['name'],
            'email' => $data['email']
        ]);

        return $professor;
    }
}