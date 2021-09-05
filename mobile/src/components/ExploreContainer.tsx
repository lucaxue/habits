import React from 'react';

interface Props {
  name: string;
}

export const ExploreContainer: React.FC<Props> = ({ name }) => {
  return (
    <div className='text-center absolute left-0 right-0 top-1/2 transform -translate-y-1/2'>
      <strong className='text-xl leading-normal'>{name}</strong>
      <p className='text-lg text-gray-400 m-0 leading-relaxed'>
        Explore{' '}
        <a
          className='no-underline text-blue-400'
          target='_blank'
          rel='noopener noreferrer'
          href='https://ionicframework.com/docs/components'
        >
          UI Components
        </a>
      </p>
    </div>
  );
};
