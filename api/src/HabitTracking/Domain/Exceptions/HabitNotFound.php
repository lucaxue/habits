<?php

namespace HabitTracking\Domain\Exceptions;

use HabitTracking\Domain\HabitId;

class HabitNotFound extends \Exception
{
    public function __construct(HabitId $id)
    {
        parent::__construct($id);
    }
}
