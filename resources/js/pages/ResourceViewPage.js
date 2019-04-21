import React from 'react';
import {Link} from 'react-router-dom';
import axios from 'axios';
import DetailComponent from "../fields/DetailComponent";

class ResourceViewPage extends React.Component {
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
    const {
      resources,
      resource,
    } = this.state;

    const {
      match: {
        params,
      },
    } = this.props;

    if (typeof resource === 'object' && resource === null) {
      return (
        <div>Loading...</div>
      )
    }

    const fields = resource.fields.filter(field => {
      return field.showOnDetail;
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
            <div className="row">
              <div className="col-md-6">
                <div className="page-heading">
                  <h1>{resource.name.singular} Details</h1>
                </div>
              </div>
              <div className="col-md-6">
                <div className="form-group text-md-right">
                  <Link
                    className="btn btn-primary btn-sm"
                    to={'/resources/' + params.resource + '/' + params.id + '/edit'}
                  >Edit</Link>
                </div>
              </div>
            </div>

            <div className="card">
              <div className="list-group list-group-flush">
                {fields.map(field =>
                  <div className="list-group-item" key={field.column}>
                    <div className="row">
                      <div className="col-xs-12 col-md-2">
                        <strong>{field.name}</strong>
                      </div>
                      <div className="col-xs-12 col-md-10">
                        <DetailComponent
                          component={field.component}
                          model={resource.model_data}
                          field={field}
                        />
                      </div>
                    </div>
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default ResourceViewPage;