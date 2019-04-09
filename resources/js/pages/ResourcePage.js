import React from 'react';
import {Link} from 'react-router-dom';
import axios from 'axios';

class ResourcePage extends React.Component {
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
      .get('/panel/api/resources/' + params.resource)
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
              <h1>{resource.name.plural}</h1>
            </div>

            <div className="form-group text-right">
              <Link
                className="btn btn-primary"
                to={'/resources/' + params.resource + '/create'}
              >{'Create ' + resource.name.singular}</Link>
            </div>

            <div className="card">
              <table className="table mb-0">
                <thead>
                  <tr>
                    {resource.indexes.map(index =>
                      <th className="border-top-0">{index.name}</th>
                    )}
                    <th className="border-top-0 text-right"></th>
                  </tr>
                </thead>

                <tbody>
                  {(resource.model_data.data).map(model =>
                    <tr key={model.id}>
                      {resource.indexes.map(index =>
                        <td>{model[index.column]}</td>
                      )}
                      <td className="text-right">
                        <Link to={'/resources/' + params.resource + '/' + model.id}>View</Link>{' '}
                        <Link to={'/resources/' + params.resource + '/' + model.id + '/edit'}>Edit</Link>{' '}
                        <Link>Delete</Link>
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default ResourcePage;