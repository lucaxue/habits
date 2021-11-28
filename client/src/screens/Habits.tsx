import React, { useEffect, useState } from 'react';
import axios from 'axios';
import { WEEKDAYS } from '../utils/constants';
import { Habit } from '../utils/types';
import { Modal } from '../components/Modal';
import { HabitView } from '../components/HabitView';

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
      <Modal
        className='w-11/12 bg-white shadow-2xl rounded-3xl h-2/3'
        {...{ showModal, setShowModal }}
      >
        {habit && (
          <HabitView
            habit={habit}
            setHabit={(habit: Habit) => {
              setHabit(habit);
              setHabits([habit, ...habits.filter(h => h.id !== habit.id)]);
            }}
            deleteHabit={(habit: Habit) =>
              setHabits(habits.filter(h => h.id !== habit.id))
            }
            closeView={() => setShowModal(false)}
          />
        )}
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
