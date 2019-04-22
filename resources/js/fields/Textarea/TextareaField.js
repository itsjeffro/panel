import React from 'react';

const TextareaField = (props) => {
  const {
    column,
    field,
    hasError,
    messageError,
    handleInputChange,
    value,
  } = props;

  return (
    <span>
      <textarea
        className={'form-control' + (hasError ? ' is-invalid' : '')}
        name={column}
        onChange={e => handleInputChange(e)}
        placeholder={field.name}
        defaultValue={value}
      />

      {hasError ? <div className="invalid-feedback">{messageError}</div> : ''}
    </span>
  )
};

export default TextareaField;