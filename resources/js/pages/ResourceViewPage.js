import React from 'react';
import {Link} from 'react-router-dom';
import axios from 'axios';
import DetailComponent from "../fields/DetailComponent";
import ResourceTable from "../components/ResourceTable";

class ResourceViewPage extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      resource: null,
      relationships: {}
    };
  }

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
        const relationships = response.data.relationships;

        this.loadRelationships(relationships);

        this.setState({resource: response.data});
      });
  }

  /**
   * Load any relationships that this resource might have.
   *
   * @param {array} relationships
   * @returns void
   */
  loadRelationships(relationships) {
    Object.keys(relationships).map((relationship) => {
      const models = relationships[relationship];

      Object.keys(models).map((model) => {
        axios
          .get('/panel/api/resources/' + models[model].table)
          .then((response) => {
            this.setState((prevState) => {
              return {
                ...prevState.relationships,
                relationships: {
                  [relationship]: {
                    [model]: response.data
                  }
                }
              }
            })
          }, (error) => {
            console.log(error);
          });
      })
    })
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
        <div>Loading...</div>
      )
    }

    const fields = resource.fields.filter(field => {
      return field.showOnDetail;
    });

    return (
      <div className="content">
        <div className="container">
          <div className="row">
            <div className="col-md-6">
              <div className="page-heading">
                <h2>{resource.name.singular} Details</h2>
              </div>
            </div>
            <div className="col-md-6">
              <div className="form-group text-md-right">
                <Link
                  className="btn btn-primary btn-sm"
                  to={'/resources/' + params.resource + '/' + params.id + '/edit'}
                >Edit</Link>
              </div>
            </div>
          </div>

          <div className="card">
            <div className="list-group list-group-flush">
              { fields.map(field =>
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
              ) }
            </div>
          </div>

          { (relationships.hasMany ? Object.keys(relationships.hasMany) : []).map((model) => {
            const resource = relationships.hasMany[model];

            return (
              <div key={resource.name.plural} className="mt-5">
                <ResourceTable
                  resourceUri={ model }
                />
              </div>
            )
          }) }
        </div>
      </div>
    )
  }
}

export default ResourceViewPage;