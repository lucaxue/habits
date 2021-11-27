import React from 'react';
import { WEEKDAYS } from '../utils/constants';
import { streakMessage } from '../utils/helpers';
import { Habit } from '../utils/types';

interface Props {
  habit: Habit;
}

export const HabitView: React.FC<Props> = ({ habit }) => {
  return (
    <div className='grid p-10'>
      <h1 className='mb-2 text-3xl font-semibold text-gray-700'>
        {habit.name}
      </h1>
      <p className='mb-8 text-xs font-semibold text-gray-700 uppercase'>
        {habit.streak ? streakMessage(habit.streak) : ''}
      </p>
      <p className='text-gray-700 capitalize'>
        <strong>Frequency</strong>: {habit.frequency.type}
      </p>
      {habit.frequency.days && (
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
  );
};
