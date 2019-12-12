<?php

namespace Wanderreisen\AbTesting\Models;

use Wanderreisen\AbTesting\AbTestingFacade;
use Wanderreisen\AbTesting\Events\ExperimentNewVisitor;
use Illuminate\Database\Eloquent\Model;

class Experiment extends Model
{
    protected $table = 'ab_experiments';

    protected $fillable = [
        'name',
        'visitors',
    ];

    protected $casts = [
        'visitors' => 'integer',
    ];

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    public function visit()
    {
        if(AbTestingFacade::isCrawler()) {
            return;
        }

        $this->increment('visitors');
        event(new ExperimentNewVisitor($this));
    }
}
