<?php

namespace HabitTracking\Domain\Exceptions;

class HabitAlreadyStopped extends \Exception
{
    public function __construct()
    {
        parent::__construct('Cannot stop a habit that has been already stopped.');
    }
}
