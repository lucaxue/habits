import axios from 'axios';
import React, { useState } from 'react';
import { Day, Frequency, Habit } from '../types';

type Weekdays = { value: Day; label: string }[];
const weekdays: Weekdays = [
  {
    value: 0,
    label: 'Sunday',
  },
  {
    value: 1,
    label: 'Monday',
  },
  {
    value: 2,
    label: 'Tuesday',
  },
  {
    value: 3,
    label: 'Wednesday',
  },
  {
    value: 4,
    label: 'Thursday',
  },
  {
    value: 5,
    label: 'Friday',
  },
  {
    value: 6,
    label: 'Saturday',
  },
];

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
      <form
        onSubmit={handleSubmit}
        className='fixed bottom-0 w-full p-8 pt-12 overflow-auto shadow-2xl h-1/2 rounded-t-3xl bg-gradient-to-b from-white via-white to-gray-50'
      >
        <div className='grid gap-4'>
          <input
            value={name}
            onChange={e => setName(e.target.value)}
            placeholder='Enter habit name'
            className='h-12 px-3 text-gray-700 border border-gray-300 rounded-lg shadow'
          />
          <select
            className='h-12 text-gray-700 border-gray-300 rounded-lg shadow'
            value={frequency.type}
            onChange={({ target: { value } }) => {
              if (!['daily', 'weekly'].includes(value)) {
                console.error(`invalid type of: ${value}`);
                return;
              }

              setFrequency(
                value === 'daily'
                  ? { type: 'daily', days: null }
                  : { type: 'weekly', days: [] }
              );
            }}
          >
            <option value='daily'>Daily</option>
            <option value='weekly'>Weekly</option>
          </select>

          <div className='flex justify-center py-2 overflow-auto'>
            {weekdays.map(w => (
              <div className='grid px-1 place-items-center' key={w.value}>
                <label
                  htmlFor={w.label}
                  className='mb-1 text-xs font-semibold text-gray-700 uppercase'
                >
                  {w.label.slice(0, 3)}
                </label>
                <input
                  disabled={frequency.type === 'daily'}
                  className='w-8 h-8 border-gray-300 rounded-lg shadow'
                  type='checkbox'
                  name={w.label}
                  checked={frequency.days?.includes(w.value)}
                  onChange={e =>
                    setFrequency({
                      ...frequency,
                      days: e.target.checked
                        ? [...(frequency.days ?? []), w.value]
                        : (frequency.days ?? []).filter(d => d !== w.value),
                    })
                  }
                />
              </div>
            ))}
          </div>
          <button
            type='submit'
            className='py-4 font-semibold text-white bg-indigo-500 rounded-lg'
          >
            Start
          </button>
        </div>
      </form>
      <pre>{JSON.stringify({ name, frequency }, null, 2)}</pre>
      <pre>{JSON.stringify(created, null, 2)}</pre>
    </div>
  );
};
