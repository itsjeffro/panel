import React from 'react';

class FormField extends React.Component {
  render() {
    const { field, hasError, messageError, handleInputChange, value } = this.props;

    return (
      <span>
      <input
        className={ 'form-control' + (hasError ? ' is-invalid' : '') }
        type="password"
        name={ field.attribute }
        onChange={ (event) => handleInputChange(event, field.attribute) }
        autoComplete={ 'new-' + field.attribute }
        placeholder={ field.name }
      />

        { hasError ? <div className="invalid-feedback">{messageError}</div> : '' }
    </span>
    )
  }
}

export default FormField;