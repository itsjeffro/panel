import React from 'react';
import {Link} from 'react-router-dom';
import axios from 'axios';
import Pagination from "../components/Pagination";
import IndexComponent from "../fields/IndexComponent";

class ResourceIndexPage extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      resources: [],
      resource: null,
      searchTimeout: null,
      search: '',
    };

    this.onPageClick = this.onPageClick.bind(this);
    this.onSearchChange = this.onSearchChange.bind(this);
  }

  componentWillMount() {
    axios
      .get('/panel/api/resources')
      .then(response => {
        this.setState({resources: response.data});
      });

    this.loadResources();
  }

  /**
   * @param page
   */
  loadResources(page) {
    const {search} = this.state;
    const {params} = this.props.match;

    let query = [];

    if (page !== undefined) {
      query.push('page=' + page);
    }

    if (search) {
      query.push('search=' + search);
    }

    const endpointQuery = query.length ? '?' + query.join('&') : '';

    axios
      .get('/panel/api/resources/' + params.resource + endpointQuery)
      .then(response => {
        this.setState({resource: response.data});
      });
  }

  /**
   * @param event
   * @param page
   */
  onPageClick(event, page) {
    event.preventDefault();

    this.loadResources(page);
  }

  /**
   * @param event
   */
  onSearchChange(event) {
    const value = event.target.value;

    if (this.state.searchTimeout) {
      clearTimeout(this.state.searchTimeout);
    }

    this.setState({
      search: value,
      searchTimeout: setTimeout(() => {
        this.loadResources();
      }, 1000)
    })
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
      return field.showOnIndex;
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
              <h1>{resource.name.plural}</h1>
            </div>

            <div className="form-group">
              <div className="row">
                <div className="col-12 col-lg-3">
                  <input
                    type="text"
                    className="form-control"
                    placeholder="Search"
                    onChange={this.onSearchChange}
                  />
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
                    {fields.map(field =>
                      <th className="border-top-0" key={field.column}>{field.name}</th>
                    )}
                    <th className="border-top-0 text-right"></th>
                  </tr>
                </thead>

                <tbody>
                  {(resource.model_data.data).map(model =>
                    <tr key={model.id}>
                      {fields.map(field =>
                        <td key={model.id + '-' + field.column}>
                          <IndexComponent
                            component={field.component}
                            model={model}
                            field={field}
                          />
                        </td>
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

export default ResourceIndexPage;