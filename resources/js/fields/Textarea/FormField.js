import React from 'react';

class FormField extends React.Component {
  componentDidMount() {
    const { handleFormDataFill, resourceName, field } = this.props;

    handleFormDataFill(field.attribute, field.value);
  }

  render() {
    const {
      field,
      hasError,
      messageError,
      handleInputChange,
      value,
    } = this.props;

    return (
      <span>
        <textarea
          className={ 'form-control' + (hasError ? ' is-invalid' : '') }
          name={ field.attribute }
          onChange={(event) => handleInputChange(event, field.attribute)}
          placeholder={ field.name }
          defaultValue={ value }
        />

        { hasError ? <div className="invalid-feedback">{ messageError }</div> : '' }
      </span>
    )
  }
}

export default FormField;
