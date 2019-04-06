import React from 'react';
import {Link} from 'react-router-dom';
import axios from 'axios';

class DashboardPage extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      resources: [],
    }
  }

  componentWillMount() {
    axios
      .get('/panel/api/resources')
      .then(response => {
        this.setState({resources: response.data});
      });
  }

  render() {
    const {resources} = this.state;

    return(
      <div className="container-fluid content">
        <div className="row">
          <div className="col-xs-12 col-md-2">
            <ul>
            {resources.map(resource =>
              <li key={resource.slug}>
                <Link to={'resources/' + resource.slug}>{resource.name}</Link>
              </li>
            )}
            </ul>
          </div>

          <div className="col-xs-12 col-md-10">
            <h1>Getting Started</h1>
          </div>
        </div>
      </div>
    )
  }
}

export default DashboardPage;