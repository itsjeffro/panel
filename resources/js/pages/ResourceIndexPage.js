import React from 'react';
import ResourceTable from "../components/ResourceTable";

class ResourceIndexPage extends React.Component {
  state = {
    resource: null,
  };

  render() {
    const { match } = this.props;

    return (
      <div className="content">
        <div className="container">
          <ResourceTable
            resourceUri={ match.params.resource }
          />
        </div>
      </div>
    )
  }
}

export default ResourceIndexPage;