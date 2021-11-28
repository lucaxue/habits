import axios from 'axios';
import React, { useState } from 'react';
import { HabitForm } from '../components/HabitForm';
import { Frequency, Habit } from '../utils/types';

export const Start: React.FC = () => {
  const [name, setName] = useState('');
  const [frequency, setFrequency] = useState<Frequency>({
    type: 'daily',
    days: null,
  });

  const [created, setCreated] = useState<Habit>();

  const handleSubmit: React.FormEventHandler<HTMLFormElement> = async e => {
    e.preventDefault();
    const { data } = await axios.post<Habit>('api/habits', { name, frequency });
    setCreated(data);
  };

  return (
    <div className='min-h-screen bg-gray-50'>
      <div className='fixed top-0 flex items-center w-full px-8 bg-gradient-to-br from-indigo-400 to-indigo-600 h-1/3 rounded-b-3xl'>
        <h1 className='text-4xl text-white'>
          Start your <strong>habit</strong>
        </h1>
      </div>
      <div className='fixed bottom-0 w-full p-8 pt-12 overflow-auto shadow-2xl h-1/2 rounded-t-3xl bg-gradient-to-b from-white via-white to-gray-50'>
        <HabitForm
          {...{
            handleSubmit,
            frequency,
            setFrequency,
            name,
            setName,
          }}
        />
      </div>
    </div>
  );
};
