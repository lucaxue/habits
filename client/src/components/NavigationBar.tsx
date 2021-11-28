import React from 'react';
import { Link } from 'react-router-dom';

export const NavigationBar: React.FC = () => (
  <nav className='fixed bottom-0 w-full'>
    <Link
      className='absolute z-10 px-4 pt-4 pb-3 transform -translate-x-1/2 bg-indigo-500 rounded-full -top-6 left-1/2'
      to='/start'
    >
      <span className='p-0 m-0 text-white fill-current material-icons'>
        add_task
      </span>
    </Link>
    <ul className='relative flex justify-between w-full p-6 bg-white shadow-2xl rounded-t-3xl'>
      <li>
        <Link to='/tracking'>
          <span className='text-indigo-600 fill-current material-icons'>
            fact_check
          </span>
        </Link>
      </li>
      <li>
        <Link to='/habits'>
          <span className='text-indigo-600 fill-current material-icons'>
            stacked_bar_chart
          </span>
        </Link>
      </li>
      <li>
        <Link to='/stats'>
          <span className='text-indigo-600 fill-current material-icons'>
            query_stats
          </span>
        </Link>
      </li>
      <li>
        <Link to='/profile'>
          <span className='text-indigo-600 fill-current material-icons'>
            face
          </span>
        </Link>
      </li>
    </ul>
  </nav>
);
