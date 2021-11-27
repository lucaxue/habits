import React from 'react';

export const Stats: React.FC = () => (
  <div className='min-h-screen bg-gray-50'>
    <div className='fixed top-0 flex items-center w-full px-8 bg-gradient-to-br from-indigo-400 to-indigo-600 h-1/3 rounded-b-3xl'>
      <h1 className='text-4xl text-white'>
        Coming <strong>Soon</strong>
      </h1>
    </div>
    <div className='fixed bottom-0 w-full py-8 overflow-auto shadow-2xl pb-28 h-1/2 rounded-t-3xl bg-gradient-to-b from-white via-white to-gray-50'></div>
  </div>
);
