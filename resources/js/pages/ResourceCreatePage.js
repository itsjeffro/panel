import React from 'react';
import {Redirect} from 'react-router-dom';
import axios from 'axios';
import FieldComponent from "../fields/FieldComponent";

class ResourceCreatePage extends React.Component {
  state = {
    error: {
      message: '',
      errors: {},
    },
    isCreated: false,
    newResource: {},
    newResourceId: null,
    resource: null,
    relationships: {},
  };

  componentWillMount() {
    const {params} = this.props.match;

    axios
      .get('/panel/api/resources/' + params.resource + '/fields')
      .then((response) => {
        this.setState({
          resource: response.data,
          relationships: response.data.relationships,
        });
      });
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
      let newResource = {
        ...prevState.newResource,
        [name]: value
      };

      return {newResource: newResource};
    });
  }

  /**
   * Process resource create request.
   */
  onHandleClick = () => {
    const {params} = this.props.match;
    const {newResource} = this.state;

    axios
      .post('/panel/api/resources/' + params.resource, newResource)
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
   * @param {object} relationships
   * @param {object} field
   * @returns {*[]}
   */
  fieldOptions(relationships, field) {
    if (field.isRelationshipField && Object.keys(relationships || {}).length) {
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
   * Returns field value.
   *
   * @param {object} resource
   * @param {object} field
   * @returns {*}
   */
  fieldValue(resource, field) {
    return ''
  }

  render() {
    const {
      match: {
        params,
      },
    } = this.props;

    const {
      error,
      isCreated,
      newResourceId,
      resource,
      relationships
    } = this.state;

    if (isCreated) {
      return <Redirect to={'/resources/' + params.resource + '/' + newResourceId} />
    }

    if (resource === null) {
      return (
        <div>Loading...</div>
      )
    }

    let resourceFields = resource.fields;

    return (
      <div className="content">
        <div className="page-heading">
          <h2>Create {resource.name.singular}</h2>
        </div>

        {error.message.length ? <div className="alert alert-danger">{error.message}</div> : ''}

        <div className="card">
          <div className="list-group list-group-flush">
            {resourceFields.map(field =>
              <div className="list-group-item" key={field.column}>
                <div className="row">
                  <div className="col-xs-12 col-md-2 pt-2">
                    <strong>{field.name}</strong>
                  </div>
                  <div className="col-xs-12 col-md-7">
                    <FieldComponent
                      errors={ error.errors }
                      field={ field }
                      handleInputChange={ this.onInputChange }
                      resource={ resource }
                      options={ this.fieldOptions(relationships, field) }
                      value={ this.fieldValue(resource, field) }
                    />
                  </div>
                </div>
              </div>
            )}
          </div>

          <div className="card-footer text-right">
            <button
              className="btn btn-primary"
              onClick={this.onHandleClick}
            >Save {name.singular}</button>
          </div>
        </div>
      </div>
    )
  }
}

export default ResourceCreatePage;