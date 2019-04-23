import React from 'react';
import {Link, Redirect} from 'react-router-dom';
import axios from 'axios';
import FieldComponent from "../fields/FieldComponent";
import Drawer from "../components/Drawer";

class ResourceEditPage extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      resource: null,
      error: {
        message: '',
        errors: {},
      },
      isUpdated: false,
    };

    this.onInputChange = this.onInputChange.bind(this);
    this.onHandleSubmit = this.onHandleSubmit.bind(this);
  }

  componentWillMount() {
    const {params} = this.props.match;

    axios
      .get('/panel/api/resources/' + params.resource + '/' + params.id)
      .then(response => {
        this.setState({resource: response.data});
      });
  }

  /**
   * Update the request data from input, textarea, select changes.
   *
   * @param event
   */
  onInputChange(event) {
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
  onHandleSubmit(event) {
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

  render() {
    const {
      error,
      isUpdated,
      resource,
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
      <div className="container-fluid content">
        <div className="row">
          <div className="col-xs-12 col-md-2">
            <Drawer/>
          </div>

          <div className="col-xs-12 col-md-10">
            <div className="page-heading">
              <h1>Edit {resource.name.singular}</h1>
            </div>

            {error.message.length ? <div className="alert alert-danger">{error.message}</div> : ''}

            <div className="card">
              <form onSubmit={e => this.onHandleSubmit(e)} autoComplete="off">
                <div className="list-group list-group-flush">
                  {fields.map(field =>
                    <div className="list-group-item" key={field.column}>
                      <div className="row">
                        <div className="col-xs-12 col-md-2 pt-2">
                          <strong>{field.name}</strong>
                        </div>
                        <div className="col-xs-12 col-md-7">
                          <FieldComponent
                            errors={error.errors}
                            field={field}
                            handleInputChange={e => this.onInputChange(e)}
                            resource={resource}
                            value={resource.model_data[field.column]}
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
                  >Update {resource.name.singular}</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default ResourceEditPage;