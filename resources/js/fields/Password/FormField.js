import React from 'react';

const FormField = (props) => {
  const {
    field,
    hasError,
    messageError,
    handleInputChange,
  } = props;

  return (
    <span>
      <input
        className={ 'form-control' + (hasError ? ' is-invalid' : '') }
        type="password"
        name={ field.column }
        onChange={ (e) => handleInputChange(e, field.attribute) }
        autoComplete={ 'new-' + field.column }
        placeholder={ field.name }
      />

      { hasError ? <div className="invalid-feedback">{messageError}</div> : '' }
    </span>
  )
};

export default FormField;