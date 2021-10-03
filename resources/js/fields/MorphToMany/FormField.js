import React from 'react';

const FormField = (props) => {
  const {
    column,
    field,
    hasError,
    messageError,
    handleInputChange,
    value,
    options,
  } = props;

  const hasErrorClass = hasError ? ' is-invalid' : '';
  const selected = value || '';

  return (
    <span>
      <select
        className={ `form-control ${hasErrorClass}` }
        name={ column }
        value={ selected }
        onChange={ (e) => handleInputChange(e, field.attribute) }
        multiple={ true }
      >
        <option value="">Choose { field.name }</option>
        { options.map((option) => (
          <option key={ option.value } value={ option.value }>{ option.label }</option>
        )) }
      </select>

      { hasError ? <div className="invalid-feedback">{ messageError }</div> : '' }
    </span>
  )
};

export default FormField;