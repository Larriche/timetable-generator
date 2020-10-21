<?php

namespace App\Jobs;

use App\Models\Timetable;
use App\Models\CollegeClass;
use App\Services\GeneticAlgorithm\Exams\Helper as ExamsHelper;
use App\Services\GeneticAlgorithm\Exams\AlgorithmController as ExamsAlgorithmController;
use App\Services\GeneticAlgorithm\Lectures\Helper as LecturesHelper;
use App\Services\GeneticAlgorithm\Lectures\AlgorithmController as LecturesAlgorithmController;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Adilarry\GALib\CrossoverTypes;
use Adilarry\GALib\MutationTypes;
use Adilarry\GALib\GeneticAlgorithmConfig;
use Adilarry\GALib\GeneticAlgorithmController;

class GenerateTimetables implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $timetable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($timetable)
    {
        $this->timetable = $timetable;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $timetable = $this->timetable;
        $exempted_days = explode(",", $timetable->days_exempted);

        $config = new GeneticAlgorithmConfig();
        $config->set_crossover_rate(0.8);
        $config->set_population_size(50);
        $config->set_mutation_rate(0.8);
        $config->set_elitism_count(4);
        $config->set_tournament_size(20);
        $config->set_temperature(1);
        $config->set_cooling_rate(0.01);
        $config->set_crossover_type(CrossoverTypes::UNIFORM);
        $config->set_mutation_type(MutationTypes::RANDOM_RESETTING);
        $config->set_adaptive_mutation(false);

        if ($timetable->type == 'EXAM') {
            $exams_start = Carbon::parse($timetable->start_date);
            $exams_end = Carbon::parse($timetable->end_date);

            while ($exams_start->lte($exams_end)) {
                $day = $exams_start->copy()->format('l');

                if (!in_array($day, $exempted_days)) {
                    \Log::info('Generating timetable');
                    $controller = new ExamsAlgorithmController($this->timetable);

                    $ga = new GeneticAlgorithmController($config, $controller);
                    $solution = $ga->run();

                    $data = ExamsHelper::parseChromosome($solution, $controller->get_scheme());
                    ExamsHelper::saveSchedule($timetable->id, $exams_start->copy()->format('Y-m-d'), $data);
                }

                $exams_start->addDay();
            }
        } else {
            $class_chunks = CollegeClass::with('block')->get()->chunk(100);

            foreach ($class_chunks as $classes) {
                $controller = new LecturesAlgorithmController($this->timetable, $classes);

                $ga = new GeneticAlgorithmController($config, $controller, 2);
                $solution = $ga->run();

                $data = LecturesHelper::parseChromosome((string) $solution, $controller->get_scheme());
                LecturesHelper::saveSchedule($timetable->id, $data, $solution, $controller);
            }
        }

        $this->timetable->update([
            'status' => 'COMPLETED'
        ]);
    }
}
