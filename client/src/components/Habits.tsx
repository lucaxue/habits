import axios from 'axios';
import React, { useEffect, useState } from 'react';

interface Habit {
  id: string;
  name: string;
  streak: string;
  completed: boolean;
  frequency: {
    type: string;
    days?: string;
  };
}

export const Habits: React.FC = () => {
  const [habits, setHabits] = useState<Habit[]>([]);

  useEffect(() => {
    (async function getHabits() {
      const { data } = await axios.get<Habit[]>('api/habits/today');
      setHabits(data);
    })();
  }, []);

  async function completeHabit(id: string) {
    const { data } = await axios.put<Habit>(`api/habits/${id}/complete`);
    setHabits([...habits.filter(({ id }) => id !== data.id), data]);
  }

  async function incompleteHabit(id: string) {
    const { data } = await axios.put<Habit>(`api/habits/${id}/incomplete`);
    setHabits([...habits.filter(({ id }) => id !== data.id), data]);
  }

  return (
    <div className='rounded-t-3xl mt-20 pt-10 bg-white'>
      {habits.map(habit => (
        <div key={habit.id} className='flex-row py-6 px-12 w-full items-center'>
          <input
            type='checkbox'
            className='mr-5'
            checked={habit.completed}
            onChange={() =>
              habit.completed
                ? incompleteHabit(habit.id)
                : completeHabit(habit.id)
            }
          />
          <div>
            <p className='font-bold text-lg w-2/3'>{habit.name}</p>
            <p className='text-sm'>{habit.streak}</p>
          </div>
        </div>
      ))}
    </div>
  );
};
