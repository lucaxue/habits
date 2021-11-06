<?php

namespace Tests\Unit\Spec;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

trait CustomMatchers
{
    public function getMatchers() : array
    {
        return [
            'beUuid' => function (string $subject) : bool {
                return Str::isUuid($subject);
            },
            'haveDateFormat' => function (
                string $subject,
                string $format
            ) : bool {

                try {
                    return Assertion::date($subject, $format);
                } catch (AssertionFailedException $e) {
                    dump($e->getMessage());
                    return false;
                }
            },
            'beNow' => function (\DateTime $subject) : bool {
                return now()->eq($subject);
            },
            'beToday' => function (\DateTime|string $subject) : bool {
                return (new Carbon($subject))->isToday();
            },
            'haveHappened' => function (Carbon $subject, Carbon $candidate) : bool {
                return $subject->eq($candidate);
            },
        ];
    }
}
