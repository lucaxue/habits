<?php

namespace HabitTracking\Domain;

use Assert\Assertion;
use Illuminate\Support\Str;

abstract class Uuid implements \JsonSerializable
{
    public static function generate() : static
    {
        return new static(Str::uuid());
    }

    public static function fromString(string $id) : static
    {
        return new static($id);
    }

    public function equals(Uuid $candidate) : bool
    {
        return
            $this->toString() === $candidate->toString() &&
            $this::class === $candidate::class;
    }

    public function toString() : string
    {
        return $this->id;
    }

    public function __toString() : string
    {
        return $this->id;
    }

    public function jsonSerialize() : string
    {
        return $this->toString();
    }

    private function __construct(private string $id)
    {
        Assertion::uuid($id);
    }
}
