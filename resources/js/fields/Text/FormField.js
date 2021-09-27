import React from 'react';

const FormField = (props) => {
  const {
    hasError,
    value,
    handleInputChange,
    field,
    messageError,
  } = props;

  return (
    <span>
        <input
          className={ 'form-control' + (hasError ? ' is-invalid' : '') }
          type="text"
          name={ field.column }
          value={ value }
          onChange={ (event) => handleInputChange(event, field.column) }
          placeholder={ field.name }
        />

      { hasError ? <div className="invalid-feedback">{ messageError }</div> : '' }
      </span>
  )
}

export default FormField;
