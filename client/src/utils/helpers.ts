export function streakMessage(streak: string): string {
  const matches = streak.match(/P(\d+)Y(\d+)M(\d+)D/);

  if (!matches) { throw new Error(`Invalid streak of: ${streak}`); }

  const [years, months, days] = Array.from(matches).slice(1).map(Number);

  const times = { years, months, days };
  const units = Object.keys(times).map(unit => unit as keyof typeof times);

  const message = units
    .filter(unit => times[unit])
    .map(unit => {
      const noun = times[unit] === 1 ? unit.slice(0, -1) : unit;
      return `${times[unit]} ${noun}`;
    })
    .reduce((last, current, index, array) => {
      if (index === 0) { return current; }

      return index === array.length - 1
        ? `${last} and ${current}`
        : `${last}, ${current}`;
    }, '');

  return message
    ? `${message} streak`
    : 'Complete today to have the first streak';
}