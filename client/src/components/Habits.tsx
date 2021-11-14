import axios from 'axios';
import React, { useEffect, useState } from 'react';
import { Habit } from '../types';

export const Habits: React.FC = () => {
  const [habits, setHabits] = useState<Habit[]>([]);

  useEffect(() => {
    (async function getHabits() {
      const { data } = await axios.get<Habit[]>('api/habits');
      setHabits(data);
    })();
  }, []);

  return (
    <div>
      {habits.map(habit => (
        <div
          key={habit.id}
          className='flex-col items-center w-full px-12 py-6 border-2 border-red-500'
        >
          <p className='w-2/3 text-lg font-bold'>{habit.name}</p>
          <p className='text-sm'>{habit.streak}</p>
        </div>
      ))}
    </div>
  );
};
