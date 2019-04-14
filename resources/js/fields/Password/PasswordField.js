import React from 'react';

const PasswordField = (props) => {
  const {
    column,
    handleInputChange
  } = props;

  return (
    <span>
      <input
        className="form-control"
        type="password"
        name={column}
        onChange={e => handleInputChange(e)}
        autoComplete="off"
      />
    </span>
  )
};

export default PasswordField;