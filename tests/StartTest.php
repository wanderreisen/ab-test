<?php

namespace Wanderreisen\AbTesting\Tests;

use Wanderreisen\AbTesting\Models\Goal;
use Wanderreisen\AbTesting\AbTestingFacade;
use Wanderreisen\AbTesting\Models\Experiment;
use Wanderreisen\AbTesting\Exceptions\InvalidConfiguration;

class StartTest extends TestCase
{
    public function test_that_start_function_works()
    {
        AbTestingFacade::pageView();

        $this->assertCount(count($this->experiments), Experiment::all());
        $this->assertCount(count($this->goals) * count($this->experiments), Goal::all());

        $everyExperimentsVisitorsIsInt = Experiment::all()->every(function ($experiment) {
            return is_int($experiment->visitors);
        });
        $this->assertTrue($everyExperimentsVisitorsIsInt);

        $everyGoalsHitIs0 = Goal::all()->every(function ($goal) {
            return $goal->hit === 0;
        });
        $this->assertTrue($everyGoalsHitIs0);
    }

    public function test_exception_if_duplicate_experiment_names()
    {
        config([
            'ab-testing.experiments' => [
                'test',
                'test',
            ],
        ]);

        $this->expectException(InvalidConfiguration::class);

        AbTestingFacade::pageView();
    }

    public function test_exception_if_duplicate_goal_names()
    {
        config([
            'ab-testing.goals' => [
                'test',
                'test',
            ],
        ]);

        $this->expectException(InvalidConfiguration::class);

        AbTestingFacade::pageView();
    }

    public function test_exception_if_no_experiments_set()
    {
        config([
            'ab-testing.experiments' => [],
        ]);

        $this->expectException(InvalidConfiguration::class);

        AbTestingFacade::pageView();
    }
}
