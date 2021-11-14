import React, { useState } from 'react';
import { useAuth } from '../hooks/useAuth';

export const Login: React.FC = () => {
  const [email, setEmail] = useState('john@example.com');
  const [password, setPassword] = useState('password');
  const { login } = useAuth();

  return (
    <form
      className='grid'
      onSubmit={async e => {
        e.preventDefault();
        await login(email, password, 'mobile');
      }}
    >
      <input
        className='mb-2 border-2'
        type='email'
        value={email}
        onChange={({ target }) => setEmail(target.value)}
      />
      <input
        className='mb-2 border-2'
        type='password'
        value={password}
        onChange={({ target }) => setPassword(target.value)}
      />
      <button type='submit'>Login</button>
    </form>
  );
};
