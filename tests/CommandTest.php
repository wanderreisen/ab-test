<?php

namespace Wanderreisen\AbTesting\Tests;

use Wanderreisen\AbTesting\Models\Goal;
use Wanderreisen\AbTesting\AbTestingFacade;
use Wanderreisen\AbTesting\Models\Experiment;
use Wanderreisen\AbTesting\Commands\ReportCommand;

class CommandTest extends TestCase
{
    public function test_flush_command()
    {
        $this->assertCount(0, Experiment::all());
        $this->assertCount(0, Goal::all());

        AbTestingFacade::pageView();

        $this->assertCount(2, Experiment::all());
        $this->assertCount(4, Goal::all());

        $this->artisan('ab:reset');

        $this->assertCount(0, Experiment::all());
        $this->assertCount(0, Goal::all());
    }

    public function test_report_command()
    {
        if (version_compare(app()->version(), '5.7.0') >= 0) {
            $this->artisan('ab:report')->assertExitCode(0);
        }

        $reportCommand = new ReportCommand;

        $this->assertEquals([
            'Experiment',
            'Visitors',
            'Goal firstGoal',
            'Goal secondGoal',
        ], $reportCommand->prepareHeader());

        $this->assertEquals([], $reportCommand->prepareBody()->toArray());

        AbTestingFacade::pageView();

        $expected = [
            [
                'firstExperiment',
                1,
                '0 (0%)',
                '0 (0%)',
            ],
            [
                'secondExperiment',
                0,
                '0 (0%)',
                '0 (0%)',
            ],
        ];
        $this->assertEquals($expected, $reportCommand->prepareBody()->toArray());

        $this->newVisitor();

        $expected = [
            [
                'firstExperiment',
                1,
                '0 (0%)',
                '0 (0%)',
            ],
            [
                'secondExperiment',
                1,
                '0 (0%)',
                '0 (0%)',
            ],
        ];
        $this->assertEquals($expected, $reportCommand->prepareBody()->toArray());

        AbTestingFacade::completeGoal('firstGoal');

        $expected = [
            [
                'firstExperiment',
                1,
                '0 (0%)',
                '0 (0%)',
            ],
            [
                'secondExperiment',
                1,
                '1 (100%)',
                '0 (0%)',
            ],
        ];
        $this->assertEquals($expected, $reportCommand->prepareBody()->toArray());

        $this->newVisitor();
        $this->newVisitor();
        $this->newVisitor();

        $expected = [
            [
                'firstExperiment',
                2,
                '0 (0%)',
                '0 (0%)',
            ],
            [
                'secondExperiment',
                3,
                '1 (33%)',
                '0 (0%)',
            ],
        ];
        $this->assertEquals($expected, $reportCommand->prepareBody()->toArray());
    }
}
