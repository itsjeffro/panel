import React from 'react';
import {Link} from 'react-router-dom';
import axios from 'axios';

class ResourceEditPage extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      resources: [],
      resource: null,
    };
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

  render() {
    const {params} = this.props.match;
    const {resources, resource} = this.state;

    if (typeof resource === 'object' && resource === null) {
      return (
        <div>Loading...</div>
      )
    }

    return (
      <div className="container-fluid content">
        <div className="row">
          <div className="col-xs-12 col-md-2">
            <ul>
              {resources.map(resource =>
                <li key={resource.slug}>
                  <Link to={'/resources/' + resource.slug}>{resource.name}</Link>
                </li>
              )}
            </ul>
          </div>

          <div className="col-xs-12 col-md-10">
            <div className="page-heading">
              <h1>Edit {resource.name.singular}</h1>
            </div>

            <div className="card">
              <div className="list-group list-group-flush">
                {resource.fields.map(field =>
                  <div className="list-group-item">
                    <div className="row">
                      <div className="col-xs-12 col-md-2 pt-2">
                        <strong>{field.name}</strong>
                      </div>
                      <div className="col-xs-12 col-md-7">
                        <input
                          className="form-control"
                          name={field.column}
                          type="text"
                          value={resource.model_data[field.column]}
                        />
                      </div>
                    </div>
                  </div>
                )}
              </div>
              <div className="card-footer text-right">
                <button className="btn btn-primary">Update {resource.name.singular}</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default ResourceEditPage;