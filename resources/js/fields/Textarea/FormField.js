import React from 'react';

const FormField = (props) => {
  const {
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
        name={ field.column }
        onChange={(e) => handleInputChange(e, field.column)}
        placeholder={field.name}
        defaultValue={value}
      />

      {hasError ? <div className="invalid-feedback">{messageError}</div> : ''}
    </span>
  )
};

export default FormField;