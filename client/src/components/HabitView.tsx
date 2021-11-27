import axios from 'axios';
import React, { useState } from 'react';
import { WEEKDAYS } from '../utils/constants';
import { streakMessage } from '../utils/helpers';
import { Frequency, Habit } from '../utils/types';
import { HabitForm } from './HabitForm';

interface Props {
  habit: Habit;
  setHabit: (habit: Habit) => void;
}

export const HabitView: React.FC<Props> = ({ habit, setHabit }) => {
  const [editing, setEditing] = useState(false);

  const [name, setName] = useState(habit.name);
  const [frequency, setFrequency] = useState<Frequency>(habit.frequency);

  const handleSubmit: React.FormEventHandler<HTMLFormElement> = async e => {
    e.preventDefault();
    const { data } = await axios.put<Habit>(`api/habits/${habit.id}`, {
      name,
      frequency,
    });
    setHabit(data);
    setEditing(false);
  };

  return (
    <div className='grid p-8'>
      <h1 className='mb-2 text-3xl font-semibold text-gray-700'>
        {name.length === 0 ? 'No Title' : name}
      </h1>
      <p className='mb-8 text-xs font-semibold text-gray-700 uppercase'>
        {habit.streak ? streakMessage(habit.streak) : ''}
      </p>

      {!editing ? (
        <>
          <p className='text-gray-700 capitalize'>
            <strong>Frequency</strong>: {frequency.type}
          </p>
          {frequency.days && (
            <p className='text-gray-700'>
              <strong>Days: </strong>
              {frequency.days
                .map(d => WEEKDAYS.find(w => w.value === d)?.label)
                .join(', ')}
            </p>
          )}
        </>
      ) : (
        <HabitForm
          {...{
            handleSubmit,
            frequency,
            setFrequency,
            name,
            setName,
            editing,
          }}
        />
      )}

      <div className='absolute flex justify-end gap-2 mt-12 bottom-10 right-10'>
        <button className='px-6 py-3 font-semibold text-white bg-red-400 rounded'>
          Stop
        </button>
        <button
          className={`px-6 py-3 font-semibold text-white rounded ${
            editing ? 'bg-gray-700' : 'bg-indigo-500'
          }`}
          onClick={() => {
            setEditing(!editing);
            setFrequency(habit.frequency);
            setName(habit.name);
          }}
        >
          {!editing ? 'Edit' : 'Cancel'}
        </button>
      </div>
    </div>
  );
};
