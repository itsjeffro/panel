import React from 'react';
import { Link } from "react-router-dom";
import { IconEye, IconEdit, IconTrash, IconPlus } from '@tabler/icons';
import axios from "axios";
import CheckBox from "../../libs/CheckBox";
import IndexComponent from "../../fields/IndexComponent";
import Pagination from "../Pagination";
import Loading from "../Loading";

class ResourceTable extends React.Component {
  state = {
    checkedRows: {},
    isDropdownBulkShown: false,
    isLoading: true,
    searchTimeout: null,
    resource: {
      actions: [],
      name: {
        singular: '',
        plural: '',
      },
      model_data: {
        data: []
      }
    },
    search: '',
  }

  componentWillMount() {
    this.loadResources();
  }

  componentDidUpdate(prevProps) {
    const {resourceUri} = this.props;
    const previousResource = prevProps.resourceUri;

    if (resourceUri !== previousResource) {
      this.loadResources();
    }
  }

  /**
   * Load resources.
   */
  loadResources = (page) => {
    const { search} = this.state;
    const { resourceUri, uriQueries } = this.props;

    let query = [];

    if (page !== undefined) {
      query.push('page=' + page);
    }

    if (search) {
      query.push('search=' + search);
    }

    if (uriQueries) {
      Object.keys(uriQueries).map((uriQuery) => {
        query.push(`${uriQuery}=${uriQueries[uriQuery]}`);
      })
    }

    const endpointQuery = query.length ? '?' + query.join('&') : '';

    this.setState({isLoading: true});

    axios
      .get('/panel/api/resources/' + resourceUri + endpointQuery)
      .then((response) => {
        this.setState({isLoading: false, resource: response.data});
      });
  }

  /**
   * Toggle bulk dropdown menu.
   */
  onDropdownBulkClick = () => {
    this.setState((prevState) => {
      return {isDropdownBulkShown: !prevState.isDropdownBulkShown}
    });
  }

  /**
   * Load paged results based on page click.
   *
   * @param {object} event
   * @param {string} page
   * @returns {void}
   */
  onPageClick = (event, page) => {
    event.preventDefault();

    this.loadResources(page);
  }

  /**
   * Handle search input change and loading resources.
   *
   * @param {object} event
   * @returns {void}
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

  /**
   * Handle delete and reload resources.
   *
   * @param {object} event
   * @param {string} resource
   * @param {string} id
   * @returns {void}
   */
  onDeleteClick = (event, resource, id) => {
    const { resourceUri } = this.props;

    axios
      .delete(`/panel/api/resources/${resourceUri}/${id}`)
      .then((response) => {
        this.loadResources();
      });
  }

  /**
   * Handles checkbox click.
   *
   * @param {object} event
   * @param {array} models
   * @param {string} indexKey
   * @returns {void}
   */
  onCheckboxChange = (event, models, indexKey) => {
    const rows = models.reduce((object, model, index) => {
      object[index] = model.resourceId;
      return object;
    }, {});

    this.setState((prevState) => {
      const checkbox = new CheckBox(prevState.checkedRows);

      return {
        checkedRows: checkbox.checkedRows(rows, indexKey),
      };
    });
  }

  /**
   * Handles action click.
   *
   * @param {object} event
   * @param {string} actionName
   * @returns void
   */
  onActionClick = (event, actionName) => {
    event.preventDefault();

    const { resourceUri } = this.props;

    axios
      .post(`/panel/api/resources/${resourceUri}/actions/${actionName}`, {
        'model_ids': this.state.checkedRows
      })
      .then((response) => {
        console.log(response.data)

        this.setState({ isDropdownBulkShown: false, checkedRows: {} })

        this.loadResources();
      });
  }

  /**
   * Returns relation uri query for.
   *
   * @returns {string}
   */
  getCreateRelationQuery() {
    const { uriQueries } = this.props;

    let query = [];

    if (uriQueries) {
      Object.keys(uriQueries).map((uriQuery) => {
        const queryName = uriQuery.charAt(0).toUpperCase() + uriQuery.slice(1);

        query.push(`via${queryName}=${uriQueries[uriQuery]}`);
      })
    }

    if (query.length === 0) {
      return '';
    }

    return '?' + query.join('&');
  }

  render() {
    const { resourceUri } = this.props;
    const { checkedRows, isDropdownBulkShown, isLoading, resource } = this.state;

    return (
      <>
        <div className="page-heading">
          <h2>{resource ? resource.name.plural : ''}</h2>
        </div>

        <div className="form-group row">
          <div className="col-12 col-lg-3">
            <input
              className="form-control form-control__search form-control--drop-shadow"
              type="text"
              placeholder="Search"
              onChange={this.onSearchChange}
            />
          </div>
          <div className="col-12 col-lg-9 text-right">
            { resource.actions.length === 0
              ? ''
              : <div className="dropdown d-inline-block mr-2">
              <button
                className="btn btn-secondary dropdown-toggle"
                onClick={ this.onDropdownBulkClick }
              >Actions
              </button>

              <div className={'dropdown-menu' + (isDropdownBulkShown ? ' show' : '')}>
                { resource.actions.map((action) => (
                  <a
                    key={ action.slug }
                    className="dropdown-item"
                    href="#"
                    onClick={ (event) => this.onActionClick(event, action.slug) }
                  >{ action.name }</a>
                ))}
              </div>
            </div> }

            <Link
              className="btn btn-primary btn-icon"
              to={ '/resources/' + resourceUri + '/create' + this.getCreateRelationQuery() }
            ><IconPlus /> { 'Create ' + (resource ? resource.name.singular : '') }</Link>
          </div>
        </div>

        {isLoading ? <Loading /> : <table className="table">
          <thead>
          <tr>
            <th width="1%">
              <input
                type="checkbox"
                onChange={ (event) => this.onCheckboxChange(event, resource.model_data.data) }
                checked={ Object.keys(checkedRows).length > 0 }
              />
            </th>
            {resource.model_data.data[0].resourceFields.map((resourceField) =>
              <th key={'th-' + resourceField.field.attribute}>
                {resourceField.field.name}
              </th>
            )}
            <th className="text-right">
              {' '}
            </th>
          </tr>
          </thead>

          <tbody>
          {(resource.model_data.data).map((model, index) =>
            <tr key={ 'tr-' + model.resourceId}>
              <td width="1%">
                <div className="form-check form-check-inline">
                  <input
                    className="form-check-input"
                    type="checkbox"
                    onChange={ (event) => this.onCheckboxChange(event, resource.model_data.data, index) }
                    checked={ checkedRows.hasOwnProperty(index) }
                  />
                </div>
              </td>
              {model.resourceFields.map((resourceField) =>
                <td key={ 'tr-' + model.resourceId + '-td-' + resourceField.field.attribute }>
                  <IndexComponent
                    component={ resourceField.component }
                    field={ resourceField.field }
                    resource={ resourceField.resource }
                    resourceId={ resourceField.resourceId }
                    resourceName={ resourceField.resourceName }
                  />
                </td>
              )}
              <td className="text-right">
                <Link
                  className="btn btn-link"
                  to={'/resources/' + resourceUri + '/' + model.resourceId}
                ><IconEye/></Link>{' '}
                <Link
                  className="btn btn-link"
                  to={'/resources/' + resourceUri + '/' + model.resourceId + '/edit'}
                ><IconEdit/></Link>{' '}
                <button
                  className="btn btn-link"
                  onClick={(event) => this.onDeleteClick(event, resourceUri, model.resourceId)}
                ><IconTrash/></button>
              </td>
            </tr>
          )}
          </tbody>
        </table>}

        {isLoading ? '' : <Pagination
          total={resource.model_data.total}
          per_page={resource.model_data.per_page}
          current_page={resource.model_data.current_page}
          handlePageClick={this.onPageClick}
        />}
      </>
    )
  }
}

export default ResourceTable;
