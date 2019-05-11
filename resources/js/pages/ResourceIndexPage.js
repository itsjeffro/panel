import React from 'react';
import {Link} from 'react-router-dom';
import axios from 'axios';
import Pagination from "../components/Pagination";
import IndexComponent from "../fields/IndexComponent";
import Drawer from "../components/Drawer";

class ResourceIndexPage extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      isDropdownBulkShown: false,
      resource: null,
      searchTimeout: null,
      search: '',
    };

    this.onPageClick = this.onPageClick.bind(this);
    this.onSearchChange = this.onSearchChange.bind(this);
    this.onDeleteClick = this.onDeleteClick.bind(this);
    this.onDropdownBulkClick = this.onDropdownBulkClick.bind(this);
  }

  componentWillMount() {
    this.loadResources();
  }

  componentDidUpdate(prevProps) {
    const {
      match: {
        params
      }
    } = this.props;

    const previousResource = prevProps.match.params.resource;

    if (params.resource !== previousResource) {
      this.loadResources();
    }
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

  /**
   * Handle delete and reload resources.
   *
   * @param event
   * @param resource
   * @param id
   */
  onDeleteClick(event, resource, id) {
    axios
      .delete('/panel/api/resources/' + resource + '/' + id)
      .then(response => {
        this.loadResources();
      });
  }

  /**
   * Toggle bulk dropdown menu.
   */
  onDropdownBulkClick() {
    this.setState(prevState => {
      return {
        isDropdownBulkShown: prevState.isDropdownBulkShown ? false : true,
      }
    });
  }

  render() {
    const {params} = this.props.match;
    const {resource, isDropdownBulkShown} = this.state;

    if (typeof resource === 'object' && resource === null) {
      return (
        <div>Loading...</div>
      )
    }

    const fields = resource.fields.filter(field => {
      return field.showOnIndex;
    });

    return (
      <div className="wrapper">
        <Drawer/>

        <main className="main-content">
          <div className="content">
            <div className="page-heading">
              <h1>{resource.name.plural}</h1>
            </div>

            <div className="form-group">
              <div className="row">
                <div className="col-12 col-lg-3">
                  <input
                    type="text"
                    className="form-control form-control--drop-shadow"
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

            <div className="table-card card">
              <div className="card-header">
                <div className="form-check form-check-inline">
                  <input className="form-check-input" type="checkbox" />

                  <div className="dropdown">
                    <button className="btn pb-0 dropdown-toggle" onClick={this.onDropdownBulkClick}></button>
                    <div className={'dropdown-menu' + (isDropdownBulkShown ? ' show' : '')}>
                      <a className="dropdown-item" href="#">Bulk Delete</a>
                    </div>
                  </div>
                </div>
              </div>

              <table className="table mb-0">
                <thead>
                  <tr>
                    <th width="1%" className="border-top-0 text-right">
                      {' '}
                    </th>
                    {fields.map(field =>
                      <th className="border-top-0" key={field.column}>{field.name}</th>
                    )}
                    <th className="border-top-0 text-right">
                      {' '}
                    </th>
                  </tr>
                </thead>

                <tbody>
                  {(resource.model_data.data).map(model =>
                    <tr key={model.id}>
                      <td width="1%">
                        <div className="form-check form-check-inline">
                          <input className="form-check-input" type="checkbox" />
                        </div>
                      </td>
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
                        <Link className="btn btn-link" to={'/resources/' + params.resource + '/' + model.id}>View</Link>{' '}
                        <Link className="btn btn-link" to={'/resources/' + params.resource + '/' + model.id + '/edit'}>Edit</Link>{' '}
                        <button
                          className="btn btn-link"
                          onClick={e => this.onDeleteClick(e, params.resource, model.id)}
                        >Delete</button>
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
        </main>
      </div>
    )
  }
}

export default ResourceIndexPage;