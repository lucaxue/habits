<?php

use App\Models\User;
use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitId;
use Illuminate\Support\Collection;
use Tests\Support\HabitModelFactory;
use HabitTracking\Domain\HabitFrequency;
use HabitTracking\Application\HabitService;
use HabitTracking\Domain\Contracts\HabitRepository;
use HabitTracking\Domain\Exceptions\HabitNotFoundException;
use HabitTracking\Domain\Exceptions\HabitDoesNotBelongToAuthorException;

it("can retrieve all of a user's habits", function () {
    $john = User::factory()->make(['id' => 1]);
    $repository = new CollectionHabitRepository(
        $habits = HabitModelFactory::count(10)->start([
            'authorId' => $john->id
        ])
    );

    $retrievedHabits = (new HabitService($repository))->retrieveHabits($john->id);

    expect($retrievedHabits)->toEqualCanonicalizing($habits);
});

it("can retrieve a user's habits for today", function () {
    $john = User::factory()->make(['id' => 1]);
    $repository = new CollectionHabitRepository(collect([
        ...$todays = HabitModelFactory::count(5)->start([
            'authorId' => $john->id,
            'frequency' => new HabitFrequency('weekly', [
                now()->dayOfWeek
            ])
        ]),
        ...$tomorrows = HabitModelFactory::count(5)->start([
            'authorId' => $john->id,
            'frequency' => new HabitFrequency('weekly', [
                now()->addDay()->dayOfWeek
            ])
        ])
    ]));

    $retrievedHabits = (new HabitService($repository))->retrieveHabitsForToday($john->id);

    expect($retrievedHabits)->toEqualCanonicalizing($todays);
});

it("can retrieve a user's habit", function () {
    $john = User::factory()->make(['id' => 1]);
    $repository = new CollectionHabitRepository(collect([
        $habit = HabitModelFactory::start([
            'id' => $id = HabitId::generate(),
            'authorId' => $john->id,
        ])
    ]));

    $retrievedHabit = (new HabitService($repository))->retrieveHabit($id->toString(), $john->id);

    expect($retrievedHabit)->toBe($habit);
});

it("can start a user's habit", function () {
    $john = User::factory()->make(['id' => 1]);
    $repository = new CollectionHabitRepository;

    $habit = (new HabitService($repository))->startHabit(
        name: 'Read a book',
        frequency: ['type' => 'weekly', 'days' => [1, 2, 3]],
        authorId: $john->id,
    );

    expect($habit)
        ->toBeInstanceOf(Habit::class)
        ->name()->toBe('Read a book')
        ->frequency()->type()->toBe('weekly')
        ->frequency()->days()->toBe([1, 2, 3]);

    expect($repository)
        ->all($john->id)
        ->toContain($habit);
});

it("can mark a user's habit as complete", function () {
    $john = User::factory()->make(['id' => 1]);
    $repository = new CollectionHabitRepository(collect([
        HabitModelFactory::start([
            'id' => $id = HabitId::generate(),
            'authorId' => $john->id,
        ])
    ]));

    $habit = (new HabitService($repository))->markHabitAsComplete($id, $john->id);

    expect($habit)
        ->toBeInstanceOf(Habit::class)
        ->completed()->toBeTrue()
        ->streak()->days()->toBe(1);

    expect($repository)
        ->find($id)
        ->toBe($habit);
});

it("can mark a user's habit as incomplete", function () {
    $john = User::factory()->make(['id' => 1]);
    $repository = new CollectionHabitRepository(collect([
        HabitModelFactory::completed([
            'id' => $id = HabitId::generate(),
            'authorId' => $john->id,
        ])
    ]));

    $habit = (new HabitService($repository))->markHabitAsIncomplete($id, $john->id);

    expect($habit)
        ->toBeInstanceOf(Habit::class)
        ->completed()->toBeFalse()
        ->streak()->days()->toBe(0);

    expect($repository)
        ->find($id)
        ->toBe($habit);
});

it("can edit a user's habit", function () {
    $john = User::factory()->make(['id' => 1]);
    $repository = new CollectionHabitRepository(collect([
        HabitModelFactory::start([
            'id' => $id = HabitId::generate(),
            'authorId' => $john->id,
            'name' => 'Read a book',
            'frequency' => new HabitFrequency('weekly', [1])
        ])
    ]));

    $habit = (new HabitService($repository))->editHabit(
        id: $id,
        name: 'Read two books',
        frequency: ['type' => 'daily', 'days' => null],
        authorId: $john->id
    );

    expect($habit)
        ->toBeInstanceOf(Habit::class)
        ->name()->toBe('Read two books')
        ->frequency()->type()->toBe('daily')
        ->frequency()->days()->toBe(null);

    expect($repository)
        ->find($id)
        ->toBe($habit);
});

it("can stop a user's habit", function () {
    $john = User::factory()->make(['id' => 1]);
    $repository = new CollectionHabitRepository(collect([
        HabitModelFactory::start([
            'id' => $id = HabitId::generate(),
            'authorId' => $john->id,
            'name' => 'Read a book',
            'frequency' => new HabitFrequency('weekly', [1])
        ])
    ]));

    $habit = (new HabitService($repository))->stopHabit($id, $john->id);

    expect($habit)
        ->toBeInstanceOf(Habit::class)
        ->stopped()->toBeTrue();
});

it("cannot manage another user's habit", function () {
    $john = User::factory()->make(['id' => 1]);
    $jane = User::factory()->make(['id' => 2]);
    $repository = new CollectionHabitRepository(collect([HabitModelFactory::start([
        'id' => $id = HabitId::generate(),
        'authorId' => $jane->id
    ])]));

    $service = new HabitService($repository);

    $actions = collect([
        fn () => $service->retrieveHabit($id, $john->id),
        fn () => $service->markHabitAsComplete($id, $john->id),
        fn () => $service->markHabitAsIncomplete($id, $john->id),
        fn () => $service->editHabit($id, 'name', ['type' => 'daily', 'days' => null], $john->id),
        fn () => $service->stopHabit($id, $john->id),
    ]);

    $actions->each(fn ($action) =>
        expect($action)->toThrow(HabitDoesNotBelongToAuthorException::class)
    );
});

class CollectionHabitRepository implements HabitRepository
{
    /** @var Collection<Habit> */
    private Collection $habits;

    /**
     * @param null|Collection<Habit> $habits
     */
    public function __construct(?Collection $habits = null)
    {
        $this->habits = $habits ?? new Collection;
    }

    /**
     * @param int $authorId
     * @param array $filters ['forToday' => bool]
     * @return Collection<Habit>
     */
    public function all(int $authorId, array $filters = []): Collection
    {
        return $this->habits->filter(function (Habit $h) use ($authorId, $filters) {
            $belongsToAuthor = $h->authorId() === $authorId;

            if (array_key_exists('forToday', $filters)) {
                return $belongsToAuthor && $h->frequency()->includesToday();
            }

            return $belongsToAuthor;
        });
    }

    /**
     * @param HabitId $id
     * @return Habit|null
     * @throws HabitNotFoundException
     */
    public function find(HabitId $id): ?Habit
    {
        return
            $this->habits->first(fn (Habit $h) => $h->id()->equals($id)) ??
            throw new HabitNotFoundException($id);
    }

    public function save(Habit $habit): void
    {
        $this->habits = $this->habits
            ->reject(fn (Habit $h) => $h->id()->equals($habit->id()))
            ->push($habit);
    }
}
