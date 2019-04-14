import React from 'react';
import {Link} from 'react-router-dom';
import axios from 'axios';
import Pagination from "../components/Pagination";

class ResourcePage extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      resources: [],
      resource: null,
    };

    this.onPageClick = this.onPageClick.bind(this);
  }

  componentWillMount() {
    const {params} = this.props.match;

    axios
      .get('/panel/api/resources')
      .then(response => {
        this.setState({resources: response.data});
      });

    this.loadResources();
  }

  loadResources(page) {
    const {params} = this.props.match;
    let currentPage = typeof page == undefined ? 1 : page;
    let pageQuery = '?page=' + currentPage;

    axios
      .get('/panel/api/resources/' + params.resource + pageQuery)
      .then(response => {
        this.setState({resource: response.data});
      });
  }

  onPageClick(event, page) {
    event.preventDefault();

    this.loadResources(page);
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

            <div className="form-group">
              <div className="row">
                <div className="col-12 col-lg-3">
                  <input type="text" className="form-control" placeholder="Search" />
                </div>
                <div className="col-12 col-lg-9 text-right">
                  <Link
                    className="btn btn-primary"
                    to={'/resources/' + params.resource + '/create'}
                  >{'Create ' + resource.name.singular}</Link>
                </div>
              </div>
            </div>

            <div className="card">
              <table className="table mb-0">
                <thead>
                  <tr>
                    {resource.fields.map(field =>
                      <th className="border-top-0" key={field.column}>{field.name}</th>
                    )}
                    <th className="border-top-0 text-right"></th>
                  </tr>
                </thead>

                <tbody>
                  {(resource.model_data.data).map(model =>
                    <tr key={model.id}>
                      {resource.fields.map(field =>
                        <td key={model.id + '-' + field.column}>{model[field.column]}</td>
                      )}
                      <td className="text-right">
                        <Link to={'/resources/' + params.resource + '/' + model.id}>View</Link>{' '}
                        <Link to={'/resources/' + params.resource + '/' + model.id + '/edit'}>Edit</Link>{' '}
                        <Link to={'/'}>Delete</Link>
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
              <div className="card-footer card-pagination">
                <Pagination
                  total={resource.model_data.total}
                  per_page={resource.model_data.per_page}
                  current_page={resource.model_data.current_page}
                  handlePageClick={this.onPageClick}
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default ResourcePage;