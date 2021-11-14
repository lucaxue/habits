export interface Habit {
  id: string;
  name: string;
  streak: string;
  completed: boolean;
  frequency: {
    type: string;
    days?: string;
  };
}