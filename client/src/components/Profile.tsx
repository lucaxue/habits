import React from 'react';
import { useAuth } from '../hooks/useAuth';

export const Profile: React.FC = () => {
  const { user, logout } = useAuth();

  return (
    <div>
      <pre>{JSON.stringify(user, null, 2)}</pre>
      <button onClick={async e => logout()}>Logout</button>
    </div>
  );
};
