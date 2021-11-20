import axios from 'axios';
import React, { useEffect, useState } from 'react';
import { useAuth } from '../hooks/useAuth';
import { Habit } from '../types';

export const Tracking: React.FC = () => {
  const [habits, setHabits] = useState<Habit[]>([]);
  const { user } = useAuth();

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
    <div className='min-h-screen bg-gray-50'>
      <div className='fixed top-0 flex items-center w-full px-8 bg-gradient-to-br from-indigo-400 to-indigo-600 h-1/3 rounded-b-3xl'>
        <h1 className='text-4xl text-white'>
          Hi, <strong>{user?.name}</strong>
        </h1>
      </div>
      <div className='fixed bottom-0 w-full py-8 pb-24 overflow-auto shadow-2xl h-1/2 rounded-t-3xl bg-gradient-to-b from-white via-white to-gray-50'>
        {habits.map(habit => (
          <div key={habit.id} className='flex items-center w-full px-8 py-4'>
            <input
              type='checkbox'
              className='w-6 h-6 mr-5 border-gray-300 rounded-full'
              checked={habit.completed}
              onChange={() =>
                habit.completed
                  ? incompleteHabit(habit.id)
                  : completeHabit(habit.id)
              }
            />
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
