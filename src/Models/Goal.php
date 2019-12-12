<?php

namespace Ben182\AbTesting\Models;

use Ben182\AbTesting\AbTestingFacade;
use Ben182\AbTesting\Events\GoalCompleted;
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
