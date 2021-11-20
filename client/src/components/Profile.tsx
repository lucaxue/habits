import React from 'react';
import { useAuth } from '../hooks/useAuth';

export const Profile: React.FC = () => {
  const { user, logout } = useAuth();

  return (
    <div className='min-h-screen bg-gray-50'>
      <div className='fixed top-0 grid w-full p-8 place-items-center h-1/3'>
        <img
          className='p-6 bg-white rounded-full h-44'
          src={`https://avatars.dicebear.com/api/bottts/${user?.name}.svg?`}
          alt={user?.name}
        />
      </div>

      <div className='fixed bottom-0 grid w-full gap-2 p-8 pb-24 overflow-auto shadow-2xl h-1/2 rounded-t-3xl bg-gradient-to-b from-white via-white to-gray-50'>
        <pre className='px-4 overflow-auto text-xs'>
          {JSON.stringify(user, null, 2)}
        </pre>
        <button
          className='font-bold text-white bg-indigo-500 rounded'
          onClick={async e => logout()}
        >
          Logout
        </button>
      </div>
    </div>
  );
};
