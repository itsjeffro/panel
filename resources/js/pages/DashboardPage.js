import React from 'react';
import Drawer from "../components/Drawer";

class DashboardPage extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    return(
      <div className="container-fluid content">
        <div className="row">
          <div className="col-xs-12 col-md-2">
            <Drawer/>
          </div>

          <div className="col-xs-12 col-md-10">
            <div className="page-heading">
              <h1>Getting Started</h1>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default DashboardPage;