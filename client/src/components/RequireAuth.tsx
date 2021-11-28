import React from 'react';
import { useAuth } from '../hooks/useAuth';
import { Navigate, Outlet } from 'react-router-dom';
import { NavigationBar } from './NavigationBar';

export const RequireAuth: React.FC = () => {
  const { authenticated } = useAuth();

  if (!authenticated) {
    return <Navigate to='/login' />;
  }

  return (
    <>
      <Outlet />
      <NavigationBar />
    </>
  );
};
