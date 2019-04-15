import React from 'react';
import {Link} from 'react-router-dom';
import axios from 'axios';
import FieldComponent from "../fields/FieldComponent";

class ResourceEditPage extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      resources: [],
      resource: null,
    };

    this.onInputChange = this.onInputChange.bind(this);
    this.onHandleSubmit = this.onHandleSubmit.bind(this);
  }

  componentWillMount() {
    const {params} = this.props.match;

    axios
      .get('/panel/api/resources')
      .then(response => {
        this.setState({resources: response.data});
      });

    axios
      .get('/panel/api/resources/' + params.resource + '/' + params.id)
      .then(response => {
        this.setState({resource: response.data});
      });
  }

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

  onHandleSubmit(event) {
    event.preventDefault();

    const {params} = this.props.match;
    const {resource} = this.state;

    axios
      .put('/panel/api/resources/' + params.resource + '/' + params.id, resource.model_data)
      .then(response => {
        //
      });
  }

  render() {
    const {params} = this.props.match;
    const {resources, resource} = this.state;

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
            <div className="drawer">
              <h3>Resources</h3>
              <ul>
                {resources.map(resource =>
                  <li key={resource.slug}>
                    <Link to={'/resources/' + resource.slug}>{resource.name}</Link>
                  </li>
                )}
              </ul>
            </div>
          </div>

          <div className="col-xs-12 col-md-10">
            <div className="page-heading">
              <h1>Edit {resource.name.singular}</h1>
            </div>

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
                            name={field.name}
                            component={field.component}
                            column={field.column}
                            value={resource.model_data[field.column]}
                            handleInputChange={e => this.onInputChange(e)}
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