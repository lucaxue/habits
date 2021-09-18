<?php

namespace Tests\Unit\Spec;

use Assert\Assertion;
use Illuminate\Support\Str;

trait CustomMatchers
{
    public function getMatchers(): array
    {
        return [
            'beUuid' => function ($subject) {
                return Str::isUuid($subject);
            },
            'haveDateFormat' => function ($subject, $format) {
                try {
                    Assertion::date($subject, $format);
                    return true;
                } catch (\Exception $e) {
                    return false;
                }
            }
        ];
    }
}
