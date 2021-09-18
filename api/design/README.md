# Habit

## Domain Layer

### Value Objects

```php
$id = HabitId::generate();
$name = new HabitName('Read a book');
$startDate = new HabitStartDate($year, $month, $day);
$frequency = new HabitFrequency(
    mon: true,
    tue: true,
    wed: true,
    thu: true,
    fri: true,
    sat: false,
    sun: false,
);

// out of scope?
$reminder = new HabitReminder(
    new Times(
        new Time(
            hour: 5,
            minutes: 30,
            meridiemIndicator: 'AM',
            timeZone: 'BST UTC+1',
        )
    )
);
```

### Domain Model

```php
Habit::plan(
    $id,
    $name,
    $startDate,
    $frequency,
    $reminder, // out of scope?
); // HabitWasPlanned

Habit::start() // HabitWasStarted

Habit::markAsComplete() // HabitWasMarkedAsComplete

Habit::markAsMissed() // HabitWasMarkedAsMissed

Habit::stop() // HabitWasStopped
```

```php
interface HabitRepository
{
    /** @return Habit[] */
    public function all(): array;
    public function find(HabitId $id): Habit;
    public function save(Habit $habit): void;
}
```

## Application Layer

```php
class HabitService
{
    public function planHabit(
        string $name,
        DateTime $startDate,
        array $frequency,
        array $reminder, // out of scope?
    ): Habit;

    public function startHabit(
        int $id
    ): bool;

    public function markHabitAsComplete(
        int $id
    ): bool;

    public function markHabitAsMissed(
        int $id
    ): bool

    public function stopHabit(
        int $id
    ): bool
}
```
