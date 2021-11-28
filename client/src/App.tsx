import React from 'react';
import {
  BrowserRouter as Router,
  Navigate,
  Routes,
  Route,
} from 'react-router-dom';
import { RequireAuth } from './components/RequireAuth';
import { Tracking } from './screens/Tracking';
import { Habits } from './screens/Habits';
import { Start } from './screens/Start';
import { Stats } from './screens/Stats';
import { Profile } from './screens/Profile';
import { Login } from './screens/Login';
import { Register } from './screens/Register';

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
