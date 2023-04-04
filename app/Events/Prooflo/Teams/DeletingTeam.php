<?php

namespace App\Events\Prooflo\Teams;

class DeletingTeam
{
    /**
     * The team instance.
     *
     * @var \Laravel\Spark\Team
     */
    public $team;

    /**
     * Create a new event instance.
     *
     * @param  \Laravel\Spark\Team  $team
     * @return void
     */
    public function __construct($team)
    {
        $this->team = $team;
    }
}
