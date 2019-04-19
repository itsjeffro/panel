import React from 'react';

const PasswordField = (props) => {
  const {
    field,
    handleInputChange,
  } = props;

  return (
    <span>
      <input
        className="form-control"
        type="password"
        name={field.column}
        onChange={e => handleInputChange(e)}
        autoComplete={'new-' + field.column}
      />
    </span>
  )
};

export default PasswordField;