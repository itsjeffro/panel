import React from 'react';

const DefaultComponent = (props) => {
  const {
    hasError,
    column,
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
          name={ column }
          value={ value }
          onChange={ handleInputChange }
          placeholder={ field.name }
        />

      { hasError ? <div className="invalid-feedback">{ messageError }</div> : '' }
      </span>
  )
}

export default DefaultComponent;
