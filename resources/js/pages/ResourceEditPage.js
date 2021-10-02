import React from 'react';
import {Redirect} from 'react-router-dom';
import axios from 'axios';
import FormFieldComponent from "../fields/FormFieldComponent";

class ResourceEditPage extends React.Component {
  state = {
    resource: null,
    error: {
      message: '',
      errors: {},
    },
    isUpdated: false,
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
      .get('/panel/api/resources/' + resource + '/' + resourceId + '/edit')
      .then((response) => {
        this.setState({ resource: response.data });
      });
  }

  /**
   * Update the request data from input, textarea, select changes.
   *
   * @param event
   * @param {string} column
   */
  onInputChange = (event, column) => {
    const name = column;
    const value = event.target.value;

    console.log(name, value);

    this.setState(prevState => {
      let resource = {
        ...prevState.resource,
        model_data: {
          ...prevState.resource.model_data,
          [name]: value
        }
      };

      return {resource: resource};
    });
  }

  /**
   * Process resource create request.
   */
  onHandleSubmit = (event) => {
    event.preventDefault();

    const {params} = this.props.match;
    const {resource} = this.state;

    axios
      .put('/panel/api/resources/' + params.resource + '/' + params.id, resource.model_data)
      .then(response => {
        this.setState({
          error: {
            message: '',
            errors: {},
          },
          isUpdated: true,
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
   * @param {object} relationships
   * @param {object} field
   * @returns {*[]}
   */
  fieldOptions = (relationships, field) => {
    if (field.isRelationshipField && Object.keys(relationships || {}).length) {
      const modelTitle = field.relation.title;

      let relationship = relationships[field.relation.type][field.relation.table]

      return relationship.model_data.data.map((option) => ({
        value: option.id,
        label: option[modelTitle],
      }))
    }

    return [];
  }

  /**
   * Return fields.
   *
   * @returns {any[]}
   */
  getFieldsFromResource = (resource) => {
    const groups = Object.keys(resource.groups || []);
    let fields = [];

    groups.map((groupKey) => {
      (resource.groups[groupKey].resourceFields || []).map((resourceField) => {
        fields.push(resourceField.field)
      })
    });

    return fields;
  }

  /**
   * Load any relationships that this resource might have.
   *
   * @param {any[]} fields
   * @returns void
   */
  getRelationshipsFromFields = (fields) => {
    const relationshipFields = fields.filter((field) => field.isRelationshipField);

    relationshipFields.map((relationshipFields) => {
      const relation = relationshipFields.relation;

      axios
        .get(`/panel/api/resources/${relation.table}`)
        .then((response) => {
          this.setState((prevState) => {
            return {
              ...prevState.relationships,
              relationships: {
                [relation.type]: {
                  [relation.table]: response.data
                }
              }
            }
          })
        }, (error) => {
          console.log(error);
        });
    })
  }

  render() {
    const {
      error,
      isUpdated,
      resource,
      relationships,
    } = this.state;

    const {
      match: {
        params,
      },
    } = this.props;

    if (isUpdated) {
      return <Redirect to={'/resources/' + params.resource + '/' + params.id} />
    }

    if (typeof resource === 'object' && resource === null) {
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
            <h2>Edit</h2>
          </div>

          { error.message.length ? <div className="alert alert-danger">{error.message}</div> : '' }

          <div className="card">
            <form onSubmit={e => this.onHandleSubmit(e)} autoComplete="off">
              <div className="list-group list-group-flush">
                { this.getFieldsFromResource(resource).map((field) => (
                  <div className="list-group-item" key={ field.column }>
                    <div className="row">
                      <div className="col-xs-12 col-md-2 pt-2">
                        <strong>{field.name}</strong>
                      </div>
                      <div className="col-xs-12 col-md-7">
                        <FormFieldComponent
                          errors={ error.errors }
                          field={ field }
                          handleInputChange={ this.onInputChange }
                          resource={ resource }
                          options={ this.fieldOptions(relationships, field) }
                          value={ field.value }
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
                >{ `Update` }</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    )
  }
}

export default ResourceEditPage;