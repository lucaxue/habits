import React, { useEffect, useState } from 'react';
import axios from 'axios';
import { dates, streakMessage } from '../utils/helpers';
import { useAuth } from '../hooks/useAuth';
import { Habit } from '../utils/types';

export const Tracking: React.FC = () => {
  const [habits, setHabits] = useState<Habit[]>([]);
  const { user } = useAuth();

  useEffect(() => {
    (async function getHabits() {
      const { data } = await axios.get<Habit[]>('api/habits?today=true');
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
        <span className='transform translate-y-1/3'>
          <h1 className='text-4xl text-white'>
            Hi, <strong>{user?.name}</strong>
          </h1>
          <p className='mt-2 text-white'>
            {habits.every(h => h.completed)
              ? 'You have completed all of your habits!'
              : `You have ${habits.filter(h => !h.completed).length} habits to
            complete.`}
          </p>
        </span>
      </div>
      <div className='absolute flex items-center w-full gap-4 px-8 py-8 overflow-auto top-1/3 h-1/6'>
        {dates(7).map(d => (
          <div
            key={d.weekday + d.day}
            className={`grid p-5 text-center uppercase shadow rounded-xl ${
              d.isToday
                ? 'bg-indigo-200 border border-indigo-500 text-indigo-500'
                : 'bg-gray-100 text-gray-500'
            }`}
          >
            <span
              className={`text-xs ${
                d.isToday ? 'font-extrabold' : 'font-semibold'
              }`}
            >
              {d.weekday}
            </span>
            <span className='text-lg font-bold'>{d.day}</span>
          </div>
        ))}
      </div>
      <div className='fixed bottom-0 w-full py-8 overflow-auto shadow-2xl pb-28 h-1/2 rounded-t-3xl bg-gradient-to-b from-white via-white to-gray-50'>
        {habits
          .sort((a, b) => +a.completed - +b.completed)
          .map(habit => (
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
                  {streakMessage(habit.streak)}
                </p>
              </div>
            </div>
          ))}
      </div>
    </div>
  );
};
