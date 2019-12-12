<?php

namespace Wanderreisen\AbTesting\Models;

use Wanderreisen\AbTesting\AbTestingFacade;
use Wanderreisen\AbTesting\Events\GoalCompleted;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $table = 'ab_goals';

    protected $fillable = [
        'name',
        'hit',
    ];

    protected $casts = [
        'hit' => 'integer',
    ];

    public function experiment()
    {
        return $this->belongsTo(Experiment::class);
    }

    public function complete()
    {
        if(AbTestingFacade::isCrawler()) {
            return;
        }

        $this->increment('hit');
        event(new GoalCompleted($this));
    }
}
