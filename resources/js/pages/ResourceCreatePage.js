import React from 'react';
import {Link} from 'react-router-dom';
import axios from 'axios';

class ResourceCreatePage extends React.Component {
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
            <ul>
              {resources.map(resource =>
                <li key={resource.slug}>
                  <Link to={'/resources/' + resource.slug}>{resource.name}</Link>
                </li>
              )}
            </ul>
          </div>

          <div className="col-xs-12 col-md-10">
            <h1>Create {resource.name.singular}</h1>

            <div className="form-group">
              <Link
                className="btn btn-primary"
                to={'/resources/' + params.resource + '/create'}
              >{'Create ' + resource.name.singular}</Link>
            </div>

            <table className="table">
              <thead>
                <tr>
                  {resource.indexes.map(index =>
                    <th>{index.name}</th>
                  )}
                  <th className="text-right"></th>
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
    )
  }
}

export default ResourceCreatePage;