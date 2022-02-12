import React, { useState } from 'react';
import { useNavigate } from 'react-router';
import { Link } from 'react-router-dom';
import { useAuth } from '../hooks/useAuth';

export const Login: React.FC = () => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');

  const [loading, setLoading] = useState(false);
  const { login } = useAuth();
  const navigate = useNavigate();

  return (
    <div className='min-h-screen bg-gray-50'>
      <div className='fixed bottom-0 w-full overflow-auto shadow-2xl h-1/2 rounded-t-3xl bg-gradient-to-b from-white via-white to-gray-50'>
        <form
          className='grid p-8'
          onSubmit={async e => {
            e.preventDefault();

            setLoading(true);
            await login(email, password, 'mobile');
            setLoading(false);

            navigate('/');
          }}
        >
          <h1 className='mx-auto mb-8 text-gray-700'>Login with email</h1>
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
            placeholder='Password'
            value={password}
            onChange={e => setPassword(e.target.value)}
          />
          <button
            className='flex justify-center py-3 mt-4 font-semibold text-white bg-indigo-500 rounded shadow'
            type='submit'
          >
            {loading && (
              <svg className='w-5 h-5 mr-3 -ml-1 text-white animate-spin' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24'>
                <circle className='opacity-25' cx='12' cy='12' r='10' stroke='currentColor' stroke-width='4'></circle>
                <path className='opacity-75' fill='currentColor' d='M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z'></path>
              </svg>
            )}
            Login
          </button>
          <span className='mx-auto mt-4 text-gray-700'>
            Don't have an account?{' '}
            <Link className='font-semibold text-gray-700' to='/register'>
              Register
            </Link>
          </span>
        </form>
      </div>
    </div>
  );
};
