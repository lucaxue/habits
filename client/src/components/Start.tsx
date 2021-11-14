import axios from 'axios';
import React, { useState } from 'react';
import { Day, Frequency, Habit } from '../types';

type Weekdays = { value: Day; label: string }[];
const weekdays: Weekdays = [
  {
    value: 0,
    label: 'Monday',
  },
  {
    value: 1,
    label: 'Tuesday',
  },
  {
    value: 2,
    label: 'Wednesday',
  },
  {
    value: 3,
    label: 'Thursday',
  },
  {
    value: 4,
    label: 'Friday',
  },
  {
    value: 5,
    label: 'Saturday',
  },
  {
    value: 6,
    label: 'Sunday',
  },
];

export const Start: React.FC = () => {
  const [name, setName] = useState('');
  const [frequency, setFrequency] = useState<Frequency>({
    type: 'daily',
    days: null,
  });

  const handleSubmit: React.FormEventHandler<HTMLFormElement> = async e => {
    e.preventDefault();
    const { data } = await axios.post<Habit>('api/habits', { name, frequency });
  };

  return (
    <div>
      <form onSubmit={handleSubmit} className='grid gap-4 rounded shadow-sm'>
        <h1>Start your new habit</h1>
        <input
          value={name}
          onChange={e => setName(e.target.value)}
          className='p-2 border-2 border-blue-300'
        />
        <select
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

        <div>
          {weekdays.map(w => (
            <div className='flex items-center' key={w.value}>
              <input
                disabled={frequency.type === 'daily'}
                className='mr-2'
                type='checkbox'
                name={w.label}
                checked={frequency.days?.includes(w.value)}
                onChange={e =>
                  setFrequency({
                    ...frequency,
                    days: e.target.checked
                      ? [...(frequency.days ?? []), w.value]
                      : [...(frequency.days ?? []).filter(d => d !== w.value)],
                  })
                }
              />
              <label htmlFor={w.label}>{w.label}</label>
            </div>
          ))}
        </div>

        <div>
          <button
            type='submit'
            className='px-4 py-2 font-semibold text-white bg-blue-400 rounded'
          >
            Start
          </button>
        </div>
      </form>
      <pre>{JSON.stringify({ name, frequency }, null, 2)}</pre>
    </div>
  );
};
