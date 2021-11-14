import React from 'react';
import { Tracking } from './components/Tracking';
import { Login } from './components/Login';
import { useAuth } from './hooks/useAuth';
import { BrowserRouter as Router, Routes, Route, Link } from 'react-router-dom';

export const App: React.FC = () => {
  const { authenticated } = useAuth();

  if (!authenticated) {
    return <Login />;
  }

  return (
    <Router>
      <Routes>
        <Route path='/tracking' element={<Tracking />} />
        <Route path='/habits' element={<p>Show all habits...</p>} />
        <Route path='/add' element={<p>Start new habit...</p>} />
        <Route path='/profile' element={<p>Profile... Logout...</p>} />
      </Routes>

      <nav className='fixed bottom-0 w-full'>
        <ul className='flex justify-between w-full p-4 border-2 border-red-500'>
          <li><Link to='/tracking'>Tracking</Link></li>
          <li><Link to='/habits'>Habits</Link></li>
          <li><Link to='/add'>Add</Link></li>
          <li><Link to='/profile'>Profile</Link></li>
        </ul>
      </nav>
    </Router>
  );
};
