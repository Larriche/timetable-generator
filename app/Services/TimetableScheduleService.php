<?php

namespace App\Services;

use App\Models\TimetableSchedule;

class TimetableScheduleService extends AbstractService
{
    /*
     * The model to be used by this service.
     *
     * @var \App\Models\TimetableSchedule
     */
    protected $model = TimetableSchedule::class;

    /**
     * Show resources with their relations.
     *
     * @var bool
     */
    protected $showWithRelations = true;
}