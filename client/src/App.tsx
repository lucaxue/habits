import React from 'react';
import {
  BrowserRouter as Router,
  Navigate,
  Routes,
  Route,
} from 'react-router-dom';
import { RequireAuth } from './components/RequireAuth';
import { Tracking } from './components/Tracking';
import { Habits } from './components/Habits';
import { Start } from './components/Start';
import { Stats } from './components/Stats';
import { Profile } from './components/Profile';
import { Login } from './components/Login';
import { Register } from './components/Register';

export const App: React.FC = () => (
  <Router>
    <Routes>
      <Route element={<RequireAuth />}>
        <Route path='/tracking' element={<Tracking />} />
        <Route path='/habits' element={<Habits />} />
        <Route path='/start' element={<Start />} />
        <Route path='/stats' element={<Stats />} />
        <Route path='/profile' element={<Profile />} />
        <Route path='/' element={<Navigate to='/tracking' />} />
      </Route>
      <Route path='/login' element={<Login />} />
      <Route path='/register' element={<Register />} />
    </Routes>
  </Router>
);
