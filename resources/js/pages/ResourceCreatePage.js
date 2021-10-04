import React from 'react';
import {Redirect} from 'react-router-dom';
import axios from 'axios';
import FormFieldComponent from "../fields/FormFieldComponent";

class ResourceCreatePage extends React.Component {
  state = {
    error: {
      message: '',
      errors: {},
    },
    isCreated: false,
    formData: {},
    newResourceId: null,
    resource: null,
    relationships: {},
  };

  componentWillMount() {
    const {params} = this.props.match;

    this.loadResource(params.resource, params.id);
  }

  /**
   * Load resource's fields.
   */
  loadResource = (resource, resourceId) => {
    axios
      .get(`/panel/api/resources/${resource}/fields`)
      .then((response) => {
        this.setState({ resource: response.data });
      });
  }

  /**
   * Update the request data from input, textarea, select changes.
   *
   * @param event
   * @param {string} attribute
   */
  onInputChange = (event, attribute) => {
    const value = event.target.value;

    this.setState((prevState) => {
      return {
        formData: {
          ...prevState.formData,
          [attribute]: value
        }
      };
    });
  }

  onFormDataFill = (attribute, value) => {
    this.setState((prevState) => {
      return {
        formData: {
          ...prevState.formData,
          [attribute]: value,
        }
      }
    })
  }

  /**
   * Process resource create request.
   */
  onHandleClick = () => {
    const { params } = this.props.match;
    const { formData } = this.state;

    axios
      .post('/panel/api/resources/' + params.resource, formData)
      .then(response => {
        this.setState({
          error: {
            message: '',
            errors: {},
          },
          isCreated: true,
          newResourceId: response.data.id,
        });
      },
        error => {
        const message = error.response.data.message || '';
        const errors = error.response.data.errors || {};

        this.setState({
          error: {
            message: message,
            errors: errors,
          }
        });
      });
  }

  /**
   * Return field options.
   *
   * @param {object} resourceField
   * @returns {*[]}
   */
  fieldOptions(resourceField) {
    if (resourceField.component === 'BelongsTo') {
      const modelTitle = field.relation.title;

      let relationship = relationships[field.relation.type][field.relation.table]

      if (relationship.model_data === undefined) {
        return []
      }

      return relationship.model_data.data.map((option) => ({
        value: option.id,
        label: option[modelTitle],
      }))
    }

    return [];
  }

  /**
   * Return fields.
   */
  getFieldsFromResource = (resource) => {
    const groups = Object.keys(resource.groups || []);

    let resourceFields = [];

    groups.map((groupKey) => {
      (resource.groups[groupKey].resourceFields || []).map((resourceField) => {
        resourceFields.push(resourceField)
      })
    });

    return resourceFields;
  }

  render() {
    const {
      match: {
        params,
      },
    } = this.props;

    const {
      error,
      formData,
      isCreated,
      newResourceId,
      resource
    } = this.state;

    if (isCreated) {
      return <Redirect to={'/resources/' + params.resource + '/' + newResourceId} />
    }

    if (resource === null) {
      return (
        <div className="content">
          <div className="container">
            Loading...
          </div>
        </div>
      )
    }

    return (
      <div className="content">
        <div className="container">
          <div className="page-heading">
            <h2>Create {resource.name.singular}</h2>
          </div>

          {error.message.length ? <div className="alert alert-danger">{error.message}</div> : ''}

          <div className="card">
            <div className="list-group list-group-flush">
              { this.getFieldsFromResource(resource).map((resourceField) => (
                <div className="list-group-item" key={ 'field-' + resourceField.field.attribute }>
                  <div className="row">
                    <div className="col-xs-12 col-md-2 pt-2">
                      <strong>{ resourceField.field.name }</strong>
                    </div>
                    <div className="col-xs-12 col-md-7">
                      <FormFieldComponent
                        component={ resourceField.component }
                        errors={ error.errors }
                        field={ resourceField.field }
                        handleInputChange={ this.onInputChange }
                        handleFormDataFill={ this.onFormDataFill }
                        resource={ resource }
                        resourceName={ resourceField.resourceName }
                        value={ formData[resourceField.field.attribute] }
                      />
                    </div>
                  </div>
                </div>
              )) }
            </div>

            <div className="card-footer text-right">
              <button
                className="btn btn-primary"
                onClick={this.onHandleClick}
              >Save {name.singular}</button>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default ResourceCreatePage;