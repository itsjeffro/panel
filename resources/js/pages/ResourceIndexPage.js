import React from 'react';
import {Link} from 'react-router-dom';
import axios from 'axios';
import ResourceTable from "../components/ResourceTable";

class ResourceIndexPage extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      isDropdownBulkShown: false,
      resource: null,
      searchTimeout: null,
      search: '',
    };

    this.onSearchChange = this.onSearchChange.bind(this);
    this.onPageClick = this.onPageClick.bind(this);
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
   * Load paged results based on page click.
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

        <ResourceTable
          onPageClick={ this.onPageClick }
          onDropdownBulkClick={ this.onDropdownBulkClick }
          onDeleteClick={ this.onDeleteClick }
          isDropdownBulkShown={ isDropdownBulkShown }
          resource={ resource }
          params={ params }
        />
      </div>
    )
  }
}

export default ResourceIndexPage;