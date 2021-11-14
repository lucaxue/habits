import React from 'react';
import { Habits } from './components/Habits';
import { Login } from './components/Login';
import { useAuth } from './hooks/useAuth';

export const App: React.FC = () => {
  const { user, authenticated } = useAuth();

  return (
    <div className='h-screen'>
      {authenticated ? <Habits /> : <Login />}
      <pre>{JSON.stringify(user, null, 2)}</pre>
    </div>
  );
};
