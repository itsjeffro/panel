import React from 'react';

class FormField extends React.Component {
  componentDidMount() {
    const { handleFormDataFill, field } = this.props;

    handleFormDataFill(field.attribute, field.value);
  }

  render() {
    const {
      field,
      handleInputChange,
      hasError,
      messageError,
      value,
    } = this.props;

    return (
      <span>
        <input
          className={'form-control' + (hasError ? ' is-invalid' : '')}
          type="text"
          name={ field.attribute }
          value={ value }
          onChange={ (event) => handleInputChange(event, field.attribute) }
          placeholder={ field.name }
        />

        { hasError ? <div className="invalid-feedback">{ messageError }</div> : '' }
        </span>
    )
  }
}

export default FormField;
