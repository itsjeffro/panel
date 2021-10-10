import React from 'react';
import {Redirect} from 'react-router-dom';
import axios from 'axios';
import FormFieldComponent from "../fields/FormFieldComponent";
import Loading from "../components/Loading";

class ResourceEditPage extends React.Component {
  state = {
    resource: {
      meta: {},
      data: [],
    },
    formData: {},
    error: {
      message: '',
      errors: {},
    },
    isLoading: true,
    isUpdated: false,
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
        this.setState({ resource: response.data, isLoading: false });
      });
  }

  /**
   * Update the request data from input, textarea, select changes.
   *
   * @param {object} event
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
  onHandleSubmit = (event) => {
    event.preventDefault();

    const { params } = this.props.match;
    const { formData } = this.state;

    axios
      .put('/panel/api/resources/' + params.resource + '/' + params.id, formData)
      .then((response) => {
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
   * Return fields.
   *
   * @returns {any[]}
   */
  getFieldsFromResource = (resource) => {
    return resource.data;
  }

  render() {
    const {
      error,
      isUpdated,
      isLoading,
      resource,
      formData,
    } = this.state;

    const {
      match: {
        params,
      },
    } = this.props;

    if (isUpdated) {
      return <Redirect to={'/resources/' + params.resource + '/' + params.id} />
    }

    if (isLoading) {
      return (
        <div className="content">
          <div className="container">
            <Loading />
          </div>
        </div>
      )
    }

    return (
      <div className="content">
        <div className="container">
          <div className="page-heading">
            <h2>Edit { resource.meta ? resource.meta.name.singular : '' }</h2>
          </div>

          { error.message.length ? <div className="alert alert-danger">{error.message}</div> : '' }

          <div className="card">
            <form onSubmit={ (event) => this.onHandleSubmit(event)} autoComplete="off">
              <div className="list-group list-group-flush">
                { this.getFieldsFromResource(resource).map((resourceField) => (
                  <div className="list-group-item" key={ 'field-' + resourceField.field.attribute }>
                    <div className="row">
                      <div className="col-xs-12 col-md-2 pt-2">
                        <strong>{ resourceField.field.name}</strong>
                      </div>
                      <div className="col-xs-12 col-md-7">
                        <FormFieldComponent
                          component={ resourceField.component }
                          errors={ error.errors }
                          field={ resourceField.field }
                          handleInputChange={ this.onInputChange }
                          handleFormDataFill={ this.onFormDataFill }
                          resource={ resourceField.resource }
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