<?php

namespace App\Services;

use App\Models\ExamIncident;

class ExamIncidentsService extends AbstractService
{
    /*
     * The model to be used by this service.
     *
     * @var \App\Models\ExamIncident
     */
    protected $model = ExamIncident::class;

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
        $incident = ExamIncident::create([
            'summary' => $data['summary'],
            'description' => $data['description'],
            'schedule_id' => $data['schedule_id']
        ]);

        return $incident;
    }
}