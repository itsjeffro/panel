import React from 'react';
import { Link } from 'react-router-dom';
import { IconEdit } from '@tabler/icons';
import axios from 'axios';
import DetailComponent from "../fields/DetailComponent";
import Loading from "../components/Loading";

class ResourceViewPage extends React.Component {
  state = {
    isLoading: true,
    resource: null
  };

  componentWillMount() {
    const {params} = this.props.match;

    this.loadResource(params.resource, params.id)
  }

  componentDidUpdate(prevProps) {
    const {params} = this.props.match;
    const previousParams= prevProps.match.params;

    if (params.resource !== previousParams.resource || params.id !== previousParams.id) {
      this.loadResource(params.resource, params.id);
    }
  }

  /**
   * Loads resource.
   */
  loadResource = (resourceUri, id) => {
    this.setState({ isLoading: true });

    axios
      .get('/panel/api/resources/' + resourceUri + '/' + id)
      .then(response => {
        this.setState({ resource: response.data, isLoading: false });
      });
  }

  /**
   * Group the fields in their respective blocks.
   *
   * @param {object} resource
   * @returns {*[]}
   */
  groupFields(resource) {
    const resourceBlockName = resource.meta.name.singular;
    let groupIndex = 0;
    let groupMap = {};

    return resource.data.reduce((groups, resource) => {
      const groupName = resource.block || `${resourceBlockName} Details`;

      if (groupMap[groupName] === undefined) {
        groupMap = {
          ...groupMap,
          [groupName]: groupIndex,
        };

        groups[groupMap[groupName]] = {
          component: resource.component,
          name: groupName,
          resourceFields: [],
        };

        groupIndex++;
      }

      groups[groupMap[groupName]].resourceFields.push(resource);

      return groups;
    }, []);
  }

  render() {
    const { isLoading, resource } = this.state;
    const {
      match: {
        params
      }
    } = this.props;

    if (isLoading) {
      return (
        <div className="content">
          <div className="container">
            <Loading />
          </div>
        </div>
      )
    }

    return (
      <div className="content">
        <div className="container">
          { this.groupFields(resource).map((group, index) => {
            return (
              <div key={'group-' + group.name}>
                <div className="row">
                  <div className="col-md-6">
                    <div className="page-heading">
                      <h2>{group.name}</h2>
                    </div>
                  </div>
                  <div className="col-md-6">
                    {index === 0 ? <div className="form-group text-md-right">
                      <Link
                        className="btn btn-primary btn-sm btn-icon"
                        to={'/resources/' + params.resource + '/' + params.id + '/edit'}
                      ><IconEdit/> Edit</Link>
                    </div> : ''}
                  </div>
                </div>

                <div className={`card mb-4`}>
                  <div className="list-group list-group-flush">
                    {group.resourceFields.map((resourceField) => (
                      <div className="list-group-item" key={'field-' + resourceField.field.attribute}>
                        <div className="row">
                          <div className="col-xs-12 col-md-2">
                            <strong>{resourceField.field.name}</strong>
                          </div>
                          <div className="col-xs-12 col-md-10">
                            <DetailComponent
                              component={resourceField.component}
                              field={resourceField.field}
                              resource={resourceField.resource}
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