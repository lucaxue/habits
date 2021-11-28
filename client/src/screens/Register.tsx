import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../hooks/useAuth';

export const Register: React.FC = () => {
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [passwordConfirmation, setPasswordConfirmation] = useState('');
  const { register } = useAuth();
  const navigate = useNavigate();

  return (
    <div className='grid min-h-screen bg-gray-50 place-items-center'>
      <form
        className='grid w-full p-8'
        onSubmit={async e => {
          e.preventDefault();
          await register(name, email, password, passwordConfirmation, 'mobile');
          navigate('/');
        }}
      >
        <h1 className='mx-auto mb-8 text-xl font-semibold text-gray-700'>
          Create your account
        </h1>
        <input
          className='h-12 mb-2 text-gray-700 border border-gray-300 rounded-lg shadow'
          type='text'
          placeholder='Name'
          value={name}
          onChange={e => setName(e.target.value)}
        />
        <input
          className='h-12 mb-2 text-gray-700 border border-gray-300 rounded-lg shadow'
          type='email'
          placeholder='Email'
          value={email}
          onChange={e => setEmail(e.target.value)}
        />
        <input
          className='h-12 mb-2 text-gray-700 border border-gray-300 rounded-lg shadow'
          type='password'
          placeholder='Enter password'
          value={password}
          onChange={e => setPassword(e.target.value)}
        />
        <input
          className='h-12 mb-2 text-gray-700 border border-gray-300 rounded-lg shadow'
          type='password'
          placeholder='Confirm password'
          value={passwordConfirmation}
          onChange={e => setPasswordConfirmation(e.target.value)}
        />
        <button
          className='py-3 mt-4 font-semibold text-white bg-indigo-500 rounded shadow'
          type='submit'
        >
          Create Account
        </button>
        <span className='mx-auto mt-4 text-gray-700'>
          Already have an account?{' '}
          <Link className='font-semibold text-gray-700' to='/login'>
            Log in
          </Link>
        </span>
      </form>
    </div>
  );
};
