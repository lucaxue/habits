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
    <div className="min-h-screen bg-gray-50">
      <div className='fixed bottom-0 w-full py-8 pb-24 overflow-auto shadow-2xl h-1/2 rounded-t-3xl bg-gradient-to-b from-white via-white to-gray-50'>
        {habits.map(habit => (
          <div key={habit.id} className='flex items-center w-full px-8 py-4'>
            <div>
              <p className='mb-1 text-sm font-semibold text-gray-700'>
                {habit.name}
              </p>
              <p className='text-xs font-semibold text-gray-400'>
                {habit.streak}
              </p>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};
