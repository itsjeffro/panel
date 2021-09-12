import React from 'react';
import IndexComponent from "../../fields/IndexComponent";
import {Link} from "react-router-dom";
import Pagination from "../Pagination";
import Input from "../Input";
import axios from "axios";

class ResourceTable extends React.Component {
  state = {
    isDropdownBulkShown: false,
    isLoading: true,
    searchTimeout: null,
    search: ''
  }

  componentWillMount() {
    this.loadResources();
  }

  componentDidUpdate(prevProps) {
    const { resourceName } = this.props;
    const previousResource = prevProps.resourceName;

    if (resourceName!== previousResource) {
      this.loadResources();
    }
  }

  /**
   * @param page
   */
  loadResources = (page) => {
    const { search } = this.state;
    const { resourceName } = this.props;

    let query = [];

    if (page !== undefined) {
      query.push('page=' + page);
    }

    if (search) {
      query.push('search=' + search);
    }

    const endpointQuery = query.length ? '?' + query.join('&') : '';

    this.setState({ isLoading: true });

    axios
      .get('/panel/api/resources/' + resourceName + endpointQuery)
      .then(response => {
        this.setState({ isLoading: false, resource: response.data });
      });
  }

  /**
   * Toggle bulk dropdown menu.
   */
  onDropdownBulkClick = () => {
    this.setState((prevState) => {
      return { isDropdownBulkShown: !prevState.isDropdownBulkShown }
    });
  }

  /**
   * Load paged results based on page click.
   */
  onPageClick = (event, page) => {
    event.preventDefault();

    this.loadResources(page);
  }

  /**
   * @param {object} event
   */
  onSearchChange = (event) => {
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
    const { onDeleteClick, resourceName } = this.props;
    const { isDropdownBulkShown, isLoading, resource } = this.state;

    if (isLoading) {
      return <>Loading...</>
    }

    return (
      <>
        <div className="form-group row">
          <div className="col-12 col-lg-3">
            <Input
              type="text"
              placeholder="Search"
              onChange={ this.onSearchChange }
            />
          </div>
          <div className="col-12 col-lg-9 text-right">
            <div className="dropdown d-inline-block mr-2">
              <button
                className="btn btn-secondary dropdown-toggle"
                onClick={ this.onDropdownBulkClick }
              >Actions</button>

              <div className={'dropdown-menu' + (isDropdownBulkShown ? ' show' : '')}>
                <a className="dropdown-item" href="#">Bulk Delete</a>
              </div>
            </div>

            <Link
              className="btn btn-primary"
              to={'/resources/' + resourceName + '/create'}
            >{'Create ' + resource.name.singular}</Link>
          </div>
        </div>

        <table className="table">
          <thead>
          <tr>
            <th width="1%">
              <input type="checkbox"/>
            </th>
            {resource.fields.map((field) =>
              <th key={field.column}>{field.name}</th>
            )}
            <th className="text-right">
              {' '}
            </th>
          </tr>
          </thead>

          <tbody>
          {(resource.model_data.data).map((model) =>
            <tr key={model.id}>
              <td width="1%">
                <div className="form-check form-check-inline">
                  <input className="form-check-input" type="checkbox"/>
                </div>
              </td>
              {resource.fields.map((field) =>
                <td key={model.id + '-' + field.column}>
                  <IndexComponent
                    component={field.component}
                    model={model}
                    field={field}
                  />
                </td>
              )}
              <td className="text-right">
                <Link className="btn btn-link" to={'/resources/' + resourceName + '/' + model.id}><span
                  className="typcn typcn-eye-outline"/></Link>{' '}
                <Link className="btn btn-link" to={'/resources/' + resourceName + '/' + model.id + '/edit'}><span
                  className="typcn typcn-edit"/></Link>{' '}
                <button
                  className="btn btn-link"
                  onClick={(e) => onDeleteClick(e, resourceName, model.id)}
                ><span className="typcn typcn-trash"/></button>
              </td>
            </tr>
          )}
          </tbody>
        </table>

        <Pagination
          total={ resource.model_data.total }
          per_page={ resource.model_data.per_page }
          current_page={ resource.model_data.current_page }
          handlePageClick={ this.onPageClick }
        />
      </>
    )
  }
}

export default ResourceTable;
