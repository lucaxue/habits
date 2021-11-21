import React, { useState } from 'react';
import { useAuth } from '../hooks/useAuth';

export const Login: React.FC = () => {
  const [email, setEmail] = useState('john@example.com');
  const [password, setPassword] = useState('password');
  const { login } = useAuth();

  return (
    <div className='min-h-screen bg-gray-50'>
      <div className='fixed bottom-0 w-full overflow-auto shadow-2xl h-1/2 rounded-t-3xl bg-gradient-to-b from-white via-white to-gray-50'>
        <form
          className='grid p-8'
          onSubmit={async e => {
            e.preventDefault();
            await login(email, password, 'mobile');
          }}
        >
          <h1 className='mx-auto mb-8 text-gray-700'>Login with email</h1>
          <input
            className='h-12 mb-2 text-gray-700 border border-gray-300 rounded-lg shadow'
            type='email'
            value={email}
            onChange={({ target }) => setEmail(target.value)}
          />
          <input
            className='h-12 mb-2 text-gray-700 border border-gray-300 rounded-lg shadow'
            type='password'
            value={password}
            onChange={({ target }) => setPassword(target.value)}
          />
          <button
            className='py-3 mt-4 font-semibold text-white bg-indigo-500 rounded shadow'
            type='submit'
          >
            Login
          </button>
        </form>
      </div>
    </div>
  );
};
