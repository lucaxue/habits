import React, { useEffect, useState } from 'react';
import axios from 'axios';
import { WEEKDAYS } from '../utils/constants';
import { Habit } from '../utils/types';
import { Modal } from '../components/Modal';
import { streakMessage } from '../utils/helpers';

export const Habits: React.FC = () => {
  const [habits, setHabits] = useState<Habit[]>([]);

  const [showModal, setShowModal] = useState(false);
  const [habit, setHabit] = useState<Habit>();

  useEffect(() => {
    (async function getHabits() {
      const { data } = await axios.get<Habit[]>('api/habits');
      setHabits(data);
    })();
  }, []);

  return (
    <div className='min-h-screen bg-gray-50'>
      <Modal {...{ showModal, setShowModal }}>
        <div className='grid p-10'>
          <h1 className='mb-2 text-3xl font-semibold text-gray-700'>
            {habit?.name}
          </h1>
          <p className='mb-8 text-xs font-semibold text-gray-700 uppercase'>
            {habit?.streak ? streakMessage(habit.streak) : ''}
          </p>
          <p className='text-gray-700 capitalize'>
            <strong>Frequency</strong>: {habit?.frequency?.type}
          </p>
          {habit?.frequency?.days && (
            <p className='text-gray-700'>
              <strong>Days: </strong>
              {habit.frequency.days
                .map(d => WEEKDAYS.find(w => w.value === d)?.label)
                .join(', ')}
            </p>
          )}
          <div className='absolute flex justify-end gap-2 mt-12 bottom-10 right-10'>
            <button className='px-6 py-3 font-semibold text-white bg-red-400 rounded-lg'>
              Stop
            </button>
            <button className='px-6 py-3 font-semibold text-white bg-indigo-500 rounded-lg'>
              Edit
            </button>
          </div>
        </div>
      </Modal>

      <div className='fixed top-0 flex items-center w-full px-8 bg-gradient-to-br from-indigo-400 to-indigo-600 h-1/3 rounded-b-3xl'>
        <h1 className='text-4xl text-white'>
          Check your <strong>habits</strong>
        </h1>
      </div>

      <div className='fixed bottom-0 w-full px-8 overflow-auto shadow-2xl pb-36 h-2/3 rounded-t-3xl'>
        <h1 className='p-2 pt-8 text-xl font-bold text-gray-700'>
          Daily Habits
        </h1>
        <div className='py-4 overflow-auto bg-white shadow-lg h-60 rounded-3xl'>
          {habits
            .filter(h => h.frequency.type === 'daily')
            .map(habit => (
              <div
                onClick={() => {
                  setShowModal(true);
                  setHabit(habit);
                }}
                key={habit.id}
                className='flex items-center w-full px-8 py-4'
              >
                <div>
                  <p className='mb-1 text-sm font-semibold text-gray-700'>
                    {habit.name}
                  </p>
                  <p className='text-xs font-semibold text-gray-400 capitalize'>
                    {habit.frequency.type}
                    {habit.frequency.days &&
                      ' - ' +
                        habit.frequency.days
                          .map(d => WEEKDAYS.find(w => w.value === d)?.label)
                          .map(w => w?.slice(0, 3))
                          .join(', ')}
                  </p>
                </div>
              </div>
            ))}
        </div>

        <h1 className='p-2 pt-8 text-xl font-bold text-gray-700'>
          Weekly Habits
        </h1>
        <div className='py-4 overflow-auto bg-white shadow-lg h-60 rounded-3xl'>
          {habits
            .filter(h => h.frequency.type === 'weekly')
            .map(habit => (
              <div
                key={habit.id}
                className='flex items-center w-full px-8 py-4'
                onClick={() => {
                  setShowModal(true);
                  setHabit(habit);
                }}
              >
                <div>
                  <p className='mb-1 text-sm font-semibold text-gray-700'>
                    {habit.name}
                  </p>
                  <p className='text-xs font-semibold text-gray-400 capitalize'>
                    {habit.frequency.type}
                    {habit.frequency.days &&
                      ' - ' +
                        habit.frequency.days
                          .map(d => WEEKDAYS.find(w => w.value === d)?.label)
                          .map(w => w?.slice(0, 3))
                          .join(', ')}
                  </p>
                </div>
              </div>
            ))}
        </div>
      </div>
    </div>
  );
};
