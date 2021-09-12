import React from 'react';
import {Link} from 'react-router-dom';
import axios from 'axios';
import ResourceTable from "../components/ResourceTable";

class ResourceIndexPage extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      resource: null,
    };

    this.onDeleteClick = this.onDeleteClick.bind(this);
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

  render() {
    const { match } = this.props;

    return (
      <div className="content">
        <div className="container">
          <ResourceTable
            onDeleteClick={ this.onDeleteClick }
            resourceName={ match.params.resource }
          />
        </div>
      </div>
    )
  }
}

export default ResourceIndexPage;