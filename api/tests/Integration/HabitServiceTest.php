<?php

use HabitTracking\Application\HabitService;

it('can start a habit', function () {
    $repository = $this->createMock();
    new HabitService($repository);
});
