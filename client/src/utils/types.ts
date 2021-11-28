export interface Habit {
  id: string;
  name: string;
  streak: string;
  completed: boolean;
  frequency: Frequency;
}

export interface Frequency {
  type: 'daily' | 'weekly';
  days: Day[] | null;
}

export type Day = 0 | 1 | 2 | 3 | 4 | 5 | 6;

export interface User {
  id: number;
  name: string;
  email: string;
  created_at: string;
}
