import React from 'react';
import { WEEKDAYS } from '../utils/constants';
import { Frequency } from '../utils/types';

interface Props {
  handleSubmit: React.FormEventHandler<HTMLFormElement>;
  frequency: Frequency;
  setFrequency: React.Dispatch<React.SetStateAction<Frequency>>;
  name: string;
  setName: React.Dispatch<React.SetStateAction<string>>;
  editing?: boolean;
}

export const HabitForm: React.FC<Props> = ({
  handleSubmit,
  frequency,
  setFrequency,
  name,
  setName,
  editing = false,
}) => {
  return (
    <form onSubmit={handleSubmit}>
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
          {WEEKDAYS.map(w => (
            <div className='grid px-1 place-items-center' key={w.value}>
              <label
                htmlFor={w.label}
                className={`mb-1 text-xs font-semibold uppercase ${
                  frequency.type === 'daily' ? 'text-gray-300' : 'text-gray-700'
                }`}
              >
                {w.label.slice(0, 3)}
              </label>
              <input
                disabled={frequency.type === 'daily'}
                className={`w-8 h-8 rounded-lg shadow ${
                  frequency.type === 'daily'
                    ? 'border-gray-100'
                    : 'border-gray-300'
                }`}
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
          className='py-3 font-semibold text-white bg-indigo-500 rounded shadow'
        >
          {!editing ? 'Start' : 'Save'}
        </button>
      </div>
    </form>
  );
};
