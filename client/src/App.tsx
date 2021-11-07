import React, { useState } from 'react';
import { useAuth } from './hooks/useAuth';

export const App: React.FC = () => {
  const [email, setEmail] = useState('john@example.com');
  const [password, setPassword] = useState('password');
  const { login, user } = useAuth();

  return (
    <div className='h-screen flex-column pt-24 px-10'>
      <form
        className='grid'
        onSubmit={async e => {
          e.preventDefault();
          await login(email, password, 'mobile');
          console.log(user);
        }}
      >
        <input
          className='border-2 mb-2'
          type='email'
          value={email}
          onChange={({ target }) => setEmail(target.value)}
        />
        <input
          className='border-2 mb-2'
          type='password'
          value={password}
          onChange={({ target }) => setPassword(target.value)}
        />
        <button type='submit'>Login</button>
      </form>
      <pre>{JSON.stringify(user, null, 2)}</pre>
    </div>
  );
};
