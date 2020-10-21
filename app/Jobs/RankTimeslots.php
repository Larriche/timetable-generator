<?php

namespace App\Jobs;

use App\Models\Timeslot;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Database\Eloquent\Collection;

class RankTimeslots implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $examTimeslots = Timeslot::forExams()->get()->toArray();
        $lectureTimeslots = Timeslot::forLectures()->get()->toArray();

        usort($examTimeslots, array('self', 'compareTimeslots'));
        usort($lectureTimeslots, array('self', 'compareTimeslots'));

        $rank = 1;

        foreach ($examTimeslots as $timeslot) {
            $timeslot = Timeslot::find($timeslot['id']);
            $timeslot->update([
                'rank' => $rank++
            ]);
        }

        $rank = 1;

        foreach ($lectureTimeslots as $timeslot) {
            $timeslot = Timeslot::find($timeslot['id']);
            $timeslot->update([
                'rank' => $rank++
            ]);
        }
    }

    /**
     * Custom comparison function to rank two timeslots
     */
    public function compareTimeslots($timeslotA, $timeslotB)
    {
        $timeslotA = (object) $timeslotA;
        $timeslotB = (object) $timeslotB;

        $partsA = Timeslot::getParts($timeslotA->time);
        $partsB = Timeslot::getParts($timeslotB->time);

        if ($partsA[0] != $partsB[0]) {
            return ($partsA[0] < $partsB[0]) ? -1 : 1;
        }

        return ($partsA[1] < $partsB[1]) ? -1 : 1;
    }
}
