import React from 'react';
import {Redirect} from 'react-router-dom';
import axios from 'axios';
import FieldComponent from "../fields/FieldComponent";
import ResourceTable from "../components/ResourceTable";

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

    axios
      .get('/panel/api/resources/' + params.resource + '/' + params.id)
      .then(response => {
        const relationships = response.data.relationships;

        this.loadRelationships(relationships);

        this.setState({ resource: response.data });
      });
  }

  /**
   * Load any relationships that this resource might have.
   *
   * @param {array} relationships
   * @returns void
   */
  loadRelationships = (relationships) => {
    Object.keys(relationships).map((relationship) => {
      const models = relationships[relationship];

      Object.keys(models).map((model) => {
        axios
          .get('/panel/api/resources/' + models[model].table)
          .then((response) => {
            this.setState((prevState) => {
              return {
                ...prevState.relationships,
                relationships: {
                  [relationship]: {
                    [model]: response.data
                  }
                }
              }
            })
          }, (error) => {
            console.log(error);
          });
      })
    })
  }

  /**
   * Update the request data from input, textarea, select changes.
   *
   * @param event
   */
  onInputChange = (event) => {
    const name = event.target.name;
    const value = event.target.value;

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
   * Returns field value.
   *
   * @param {object} resource
   * @param {object} field
   * @returns {*}
   */
  fieldValue = (resource, field) => {
    if (field.isRelationshipField) {
      const foreignKey = field.relation.foreign_key;

      return resource.model_data[foreignKey]
    }

    return resource.model_data[field.column]
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
        <div>Loading...</div>
      )
    }

    const fields = resource.fields.filter(field => {
      return field.showOnUpdate;
    });

    return (
      <div className="content">
        <div className="container">
          <div className="page-heading">
            <h2>Edit {resource.name.singular}</h2>
          </div>

          { error.message.length ? <div className="alert alert-danger">{error.message}</div> : '' }

          <div className="card">
            <form onSubmit={e => this.onHandleSubmit(e)} autoComplete="off">
              <div className="list-group list-group-flush">
                { fields.map((field) => (
                  <div className="list-group-item" key={field.column}>
                    <div className="row">
                      <div className="col-xs-12 col-md-2 pt-2">
                        <strong>{field.name}</strong>
                      </div>
                      <div className="col-xs-12 col-md-7">
                        <FieldComponent
                          errors={ error.errors }
                          field={ field }
                          handleInputChange={ (e) => this.onInputChange(e) }
                          resource={resource}
                          options={ this.fieldOptions(relationships, field) }
                          value={ this.fieldValue(resource, field) }
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
                >Update {resource.name.singular}</button>
              </div>
            </form>
          </div>

          { (relationships.hasMany ? Object.keys(relationships.hasMany) : []).map((model) => {
            const resource = relationships.hasMany[model];

            return (
              <div key={resource.name.plural} className="mt-5">
                <ResourceTable
                  onDeleteClick={ this.onDeleteClick }
                  resourceUri={ model }
                />
              </div>
            )
          }) }
        </div>
      </div>
    )
  }
}

export default ResourceEditPage;