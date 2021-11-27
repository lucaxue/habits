import React from 'react';

interface Props {
  showModal: boolean;
  setShowModal: React.Dispatch<React.SetStateAction<boolean>>;
  children: React.ReactNode;
}

export const Modal: React.FC<Props> = ({
  showModal,
  setShowModal,
  children,
}) => {
  if (!showModal) {
    return <></>;
  }
  return (
    <>
      <div
        className='fixed z-20 w-full min-h-screen bg-gray-800 opacity-40'
        onClick={() => setShowModal(false)}
      />
      <div className='fixed z-30 w-11/12 transform -translate-x-1/2 -translate-y-1/2 bg-white shadow-2xl top-1/2 left-1/2 rounded-3xl h-2/3'>
        {children}
      </div>
    </>
  );
};
