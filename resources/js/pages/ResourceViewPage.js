import React from 'react';
import {Link} from 'react-router-dom';
import axios from 'axios';
import DetailComponent from "../fields/DetailComponent";
import ResourceTable from "../components/ResourceTable";

class ResourceViewPage extends React.Component {
  state = {
    resource: null
  };

  componentWillMount() {
    const {params} = this.props.match;

    this.loadResource(params.resource, params.id)
  }

  componentDidUpdate(prevProps) {
    const {params} = this.props.match;
    const previousResource = prevProps.match.params.resource;

    if (params.resource !== previousResource) {
      this.loadResource(params.resource, params.id);
    }
  }

  /**
   * Loads resource.
   */
  loadResource = (resourceUri, id) => {
    axios
      .get('/panel/api/resources/' + resourceUri + '/' + id)
      .then(response => {
        this.setState({resource: response.data});
      });
  }

  render() {
    const { resource } = this.state;
    const {
      match: {
        params,
      },
    } = this.props;

    if (typeof resource === 'object' && resource === null) {
      return (
        <div className="content">
          <div className="container">
            Loading...
          </div>
        </div>
      )
    }

    return (
      <div className="content">
        <div className="container">
          {Object.keys(resource.groups).map((groupKey, index) => {
            const group = resource.groups[groupKey];

            if (group.component === 'HasMany') {
              return (
                <div key={ group.resourceName } className="page-heading">
                  <ResourceTable
                    resourceUri={ group.resourceName }
                    // uriQuery={ `relation[${group.relation.foreign_key}]=${resource.model_data.id}` }
                  />
                </div>
              )
            }

            if (typeof group.resourceFields == 'undefined' || group.resourceFields.length === 0) {
              return <></>
            }

            return (
              <div key={'group-' + groupKey}>
                <div className="row">
                  <div className="col-md-6">
                    <div className="page-heading">
                      <h2>{group.name}</h2>
                    </div>
                  </div>
                  <div className="col-md-6">
                    {index === 0 ? <div className="form-group text-md-right">
                      <Link
                        className="btn btn-primary btn-sm"
                        to={'/resources/' + params.resource + '/' + params.id + '/edit'}
                      >Edit</Link>
                    </div> : ''}
                  </div>
                </div>

                <div className="card mb-4">
                  <div className="list-group list-group-flush">
                    {group.resourceFields.map((resourceField) => (
                      <div className="list-group-item" key={ 'field-' + resourceField.field.attribute }>
                        <div className="row">
                          <div className="col-xs-12 col-md-2">
                            <strong>{resourceField.field.name}</strong>
                          </div>
                          <div className="col-xs-12 col-md-10">
                            <DetailComponent
                              component={resourceField.component}
                              field={resourceField.field}
                              resourceId={resourceField.resourceId}
                              resourceName={resourceField.resourceName}
                            />
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            )
          })}
        </div>
      </div>
    )
  }
}

export default ResourceViewPage;