import React from 'react';
import axios from "axios";

class FormField extends React.Component {
  state = {
    options: []
  }

  componentDidMount() {
    const { handleFormDataFill, resource, resourceId, resourceName, field } = this.props;

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
      value
    } = this.props;

    const { options } = this.state;

    return (
      <span>
        <select
          className={ `form-control ${hasError ? ' is-invalid' : ''}` }
          name={ field.column }
          value={ value || '' }
          onChange={ (event) => handleInputChange(event, field.attribute) }
        >
          <option value="">Choose { field.name }</option>
          { (options || []).map((option) => (
            <option key={ option.value } value={ option.value }>{ option.label }</option>
          )) }
        </select>

        { hasError ? <div className="invalid-feedback">{ messageError }</div> : '' }
      </span>
    )
  }
}

export default FormField;
