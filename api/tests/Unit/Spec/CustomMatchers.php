<?php

namespace Tests\Unit\Spec;

use Illuminate\Support\Str;

trait CustomMatchers
{
    public function getMatchers(): array
    {
        return [
            'beUuid' => function ($subject) {
                return Str::isUuid($subject);
            },
        ];
    }
}
