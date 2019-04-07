import React from 'react';
import {Link} from 'react-router-dom';
import axios from 'axios';

class ResourcePage extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      resource: null,
    };
  }

  componentWillMount() {
    const {params} = this.props.match;

    axios
      .get('/panel/api/resources/' + params.resource)
      .then(response => {
        this.setState({resource: response.data});
      });
  }

  render() {
    const {params} = this.props.match;
    const {resource} = this.state;

    if (typeof resource === 'object' && resource === null) {
      return (
        <div>Loading...</div>
      )
    }

    return (
      <div className="container-fluid content">
        <h1>{resource.name.plural}</h1>

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
    )
  }
}

export default ResourcePage;