import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';

export const Register: React.FC = () => {
  const [email, setEmail] = useState('john@example.com');
  const [password, setPassword] = useState('password');
  const navigate = useNavigate();

  return (
    <div className='grid min-h-screen bg-gray-50 place-items-center'>
      <form
        className='grid w-full p-8'
        onSubmit={async e => {
          // register
          navigate('/');
        }}
      >
        <h1 className='mx-auto mb-8 text-gray-700'>Create your account</h1>
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
          Create Account
        </button>
      </form>
    </div>
  );
};
