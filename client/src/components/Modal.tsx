import React from 'react';

interface Props {
  showModal: boolean;
  setShowModal: React.Dispatch<React.SetStateAction<boolean>>;
  children: React.ReactNode;
  className?: string;
  backgroundClassName?: string;
}

export const Modal: React.FC<Props> = ({
  showModal,
  setShowModal,
  children,
  className = '',
  backgroundClassName = '',
}) => {
  if (!showModal) {
    return <></>;
  }
  return (
    <>
      <div
        className={`fixed z-20 w-full h-full bg-gray-700 opacity-40 ${backgroundClassName}`}
        onClick={() => setShowModal(false)}
      />
      <div
        className={`fixed z-30 transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2 ${className}`}
      >
        {children}
      </div>
    </>
  );
};
