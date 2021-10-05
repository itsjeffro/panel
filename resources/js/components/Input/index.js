import React from 'react';

const Input = (props) => {
  const inputProps = props;

  return (
    <input
      className="form-control form-control--drop-shadow"
      { ...inputProps }
    />
  )
}

export default Input;
