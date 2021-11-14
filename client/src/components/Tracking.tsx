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

export const Tracking: React.FC = () => {
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
    <div className='pt-10 mt-20 bg-white rounded-t-3xl'>
      {habits.map(habit => (
        <div key={habit.id} className='flex-row items-center w-full px-12 py-6'>
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
            <p className='w-2/3 text-lg font-bold'>{habit.name}</p>
            <p className='text-sm'>{habit.streak}</p>
          </div>
        </div>
      ))}
    </div>
  );
};
