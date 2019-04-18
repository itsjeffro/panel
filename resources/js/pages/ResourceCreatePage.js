import React from 'react';
import {Link} from 'react-router-dom';
import axios from 'axios';
import FieldComponent from "../fields/FieldComponent";

class ResourceCreatePage extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      resources: [],
      resource: {},
      newResource: {},
    };

    this.onInputChange = this.onInputChange.bind(this);
    this.onHandleClick = this.onHandleClick.bind(this);
  }

  componentWillMount() {
    const {params} = this.props.match;

    axios
      .get('/panel/api/resources')
      .then(response => {
        this.setState({
          resources: response.data
        });
      });

    axios
      .get('/panel/api/resources/' + params.resource + '/fields')
      .then(response => {
        this.setState({
          resource: response.data,
        });
      });
  }

  onInputChange(event) {
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

  onHandleClick() {
    const {params} = this.props.match;
    const {newResource} = this.state;

    axios
      .post('/panel/api/resources/' + params.resource, newResource)
      .then(response => {
        //
      });
  }

  render() {
    const {
      resources,
      resource
    } = this.state;

    if (resource.fields === undefined) {
      return (
        <div>Loading...</div>
      )
    }

    let resourceFields = resource.fields.filter(field => {
      return field.showOnCreate;
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
              <h1>Create {resource.name.singular}</h1>
            </div>

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
                          resource={resource}
                          field={field}
                          value={this.state.newResource[field.column] || ''}
                          handleInputChange={this.onInputChange}
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
        </div>
      </div>
    )
  }
}

export default ResourceCreatePage;