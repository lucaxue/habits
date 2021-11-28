export function streakMessage(streak: string): string {
  const matches = streak.match(/P(\d+)Y(\d+)M(\d+)D/);

  if (!matches) {
    throw new Error(`Invalid streak of: ${streak}`);
  }

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
      if (index === 0) {
        return current;
      }

      return index === array.length - 1
        ? `${last} and ${current}`
        : `${last}, ${current}`;
    }, '');

  return message
    ? `${message} streak`
    : 'Complete today to have the first streak';
}

export function dates(days: number): {
  weekday: string;
  day: string;
  isToday: boolean;
}[] {
  return Array(days)
    .fill(new Date())
    .map((d, i) => {
      const date = new Date();
      date.setDate(d.getDate() + i);

      return {
        weekday: date.toLocaleDateString('en-EN', { weekday: 'short' }),
        day: date.toLocaleDateString('en-EN', { day: 'numeric' }),
        isToday: date.toDateString() === new Date().toDateString(),
      };
    });
}

export function daysBetween(a: Date, b: Date): number {
  const MS_PER_DAY = 1000 * 60 * 60 * 24;

  // Discard the time and time-zone information.
  const utc1 = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate());
  const utc2 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());

  return Math.floor((utc2 - utc1) / MS_PER_DAY);
}

export function durationFrom(initialDate: Date): {
  amount: number;
  unit: string;
} {
  const days = daysBetween(initialDate, new Date());

  if (days > 365) {
    const years = Math.floor(days / 365);
    return {
      amount: years,
      unit: `year${years > 1 ? 's' : ''}`,
    };
  }

  if (days > 30) {
    const months = Math.floor(days / 30);
    return {
      amount: months,
      unit: `month${months > 1 ? 's' : ''}`,
    };
  }

  return {
    amount: days,
    unit: `day${days > 1 ? 's' : ''}`,
  };
}
