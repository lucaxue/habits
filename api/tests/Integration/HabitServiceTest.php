<?php

use App\Models\User;
use HabitTracking\Application\HabitService;
use HabitTracking\Domain\Contracts\HabitRepository;
use HabitTracking\Domain\Exceptions\HabitDoesNotBelongToAuthor;
use HabitTracking\Domain\Exceptions\HabitNotFound;
use HabitTracking\Domain\Habit;
use HabitTracking\Domain\HabitFrequency;
use HabitTracking\Domain\HabitId;
use Illuminate\Support\Collection;
use Tests\Support\HabitModelFactory;

it("can retrieve a user's habit", function () {
    $john = User::factory()->make(['id' => 1]);
    $repository = new CollectionHabitRepository(collect([
        $habit = HabitModelFactory::start([
            'id' => $id = HabitId::generate(),
            'authorId' => $john->id,
        ]),
    ]));

    $retrievedHabit = (new HabitService($repository))->retrieveHabit($id->toString(), $john->id);

    expect($retrievedHabit)->toBe($habit);
});

it('can start a habit', function () {
    $john = User::factory()->make(['id' => 1]);
    $repository = new CollectionHabitRepository();

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
        ]),
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
        ]),
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
            'frequency' => new HabitFrequency('weekly', [1]),
        ]),
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
            'frequency' => new HabitFrequency('weekly', [1]),
        ]),
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
        'authorId' => $jane->id,
    ])]));

    $service = new HabitService($repository);

    $actions = collect([
        fn () => $service->retrieveHabit($id, $john->id),
        fn () => $service->markHabitAsComplete($id, $john->id),
        fn () => $service->markHabitAsIncomplete($id, $john->id),
        fn () => $service->editHabit($id, 'name', ['type' => 'daily', 'days' => null], $john->id),
        fn () => $service->stopHabit($id, $john->id),
    ]);

    $actions->each(
        fn ($action) => expect($action)->toThrow(HabitDoesNotBelongToAuthor::class)
    );
});

class CollectionHabitRepository implements HabitRepository
{
    /** @var Collection<Habit> */
    private Collection $habits;

    /**
     * @param null|Collection<Habit> $habits
     */
    public function __construct(? Collection $habits = null)
    {
        $this->habits = $habits ?? new Collection();
    }

    /**
     * @param HabitId $id
     * @return Habit|null
     * @throws HabitNotFound
     */
    public function find(HabitId $id) : ? Habit
    {
        $habit = $this->habits->first(fn (Habit $h) => $h->id()->equals($id));

        if ( ! $habit) {
            throw new HabitNotFound($id);
        }

        return $habit;
    }

    public function save(Habit $habit) : void
    {
        $this->habits = $this->habits
            ->reject(fn (Habit $h) => $h->id()->equals($habit->id()))
            ->push($habit);
    }
}
