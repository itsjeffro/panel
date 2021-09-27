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

  loadResource = (resourceUri, id) => {
    axios
      .get('/panel/api/resources/' + resourceUri + '/' + id)
      .then(response => {
        this.setState({resource: response.data});
      });
  }

  render() {
    const { resource, relationships } = this.state;
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
          { Object.keys(resource.groups).map((groupKey, index) => {
            const group = resource.groups[groupKey];

            if (group.hasOwnProperty('relation')) {
              return (
                <div className="page-heading">
                  <ResourceTable
                    resourceUri={group.relation.table}
                    uriQuery={ `relation[${group.relation.column}]=${resource.model_data.id}` }
                  />
                </div>
              )
            }

            if (group.fields.length === 0) {
              return <></>
            }

            return (
              <div key={ groupKey }>
                <div className="row">
                  <div className="col-md-6">
                    <div className="page-heading">
                      <h2>{ group.name }</h2>
                    </div>
                  </div>
                  <div className="col-md-6">
                    { index === 0 ? <div className="form-group text-md-right">
                      <Link
                        className="btn btn-primary btn-sm"
                        to={'/resources/' + params.resource + '/' + params.id + '/edit'}
                      >Edit</Link>
                    </div> : '' }
                  </div>
                </div>

                <div className="card mb-4">
                  <div className="list-group list-group-flush">
                    { group.fields.map((field) => (
                      <div className="list-group-item" key={field.column}>
                        <div className="row">
                          <div className="col-xs-12 col-md-2">
                            <strong>{field.name}</strong>
                          </div>
                          <div className="col-xs-12 col-md-10">
                            <DetailComponent
                              component={field.component}
                              model={resource.model_data}
                              field={field}
                            />
                          </div>
                        </div>
                      </div>
                    )) }
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