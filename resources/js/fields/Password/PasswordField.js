import React from 'react';

const PasswordField = (props) => {
  const {
    column,
    field,
    hasError,
    messageError,
    handleInputChange,
  } = props;

  return (
    <span>
      <input
        className={'form-control' + (hasError ? ' is-invalid' : '')}
        type="password"
        name={column}
        onChange={e => handleInputChange(e)}
        autoComplete={'new-' + column}
        placeholder={field.name}
      />

      {hasError ? <div className="invalid-feedback">{messageError}</div> : ''}
    </span>
  )
};

export default PasswordField;