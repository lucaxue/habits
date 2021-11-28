import React from 'react';
import { useAuth } from '../hooks/useAuth';
import { durationFrom } from '../utils/helpers';

export const Profile: React.FC = () => {
  const { user, logout } = useAuth();
  const memberDuration = durationFrom(new Date(user?.created_at ?? new Date()));

  return (
    <div className='min-h-screen bg-white'>
      <div className='fixed top-0 flex items-end justify-center w-full px-8 bg-gradient-to-br from-indigo-400 to-indigo-600 h-1/3 rounded-b-3xl'>
        <img
          className='absolute p-6 bg-white rounded-full shadow-xl -bottom-1/3 h-44'
          src={`https://avatars.dicebear.com/api/bottts/${user?.name}.svg?`}
          alt={user?.name}
        />
      </div>

      <div className='fixed bottom-0 flex-col w-full px-8 pb-32 h-1/2 bg-gradient-to-b from-white via-white to-gray-50'>
        <div className='text-center'>
          <h1 className='text-3xl font-semibold text-gray-700'>{user?.name}</h1>
          <p className='text-gray-500'>
            Joined {memberDuration.amount} {memberDuration.unit} ago
          </p>
        </div>
        <button
          className='w-full py-3 mt-8 font-semibold text-white bg-indigo-500 rounded shadow'
          onClick={async e => logout()}
        >
          Logout
        </button>
      </div>
    </div>
  );
};
