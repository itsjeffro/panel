import React from 'react';
import axios from "axios";

class FormField extends React.Component {
  state = {
    options: []
  }

  componentDidMount() {
    const { handleFormDataFill, resource, resourceName, field } = this.props;

    handleFormDataFill(field.attribute, field.value);

    this.loadOptions(resource);
  }

  loadOptions(resource) {
    axios
      .get(`/panel/api/resources/${resource}`)
      .then((response) => {
        const items = response.data.data.map((item) => ({
          value: item.resourceId,
          label: item.resourceName,
        }));

        this.setState({ options: items })
      }, (error) => {
        console.log(error);
      });
  }

  render() {
    const {
      field,
      hasError,
      messageError,
      handleInputChange,
      value,
    } = this.props;

    const hasErrorClass = hasError ? ' is-invalid' : '';
    const selected = value || '';

    return (
      <span>
        <select
          className={ `form-control ${hasErrorClass}` }
          name={ field.name }
          value={ selected }
          onChange={ (event) => handleInputChange(event, field.attribute) }
        >
          <option value="">Choose {field.name}</option>
          { this.state.options.map((option) => (
            <option key={ option.value } value={ option.value }>{ option.label }</option>
          )) }
        </select>

        { hasError ? <div className="invalid-feedback">{messageError}</div> : '' }
      </span>
    )
  }
}

export default FormField;