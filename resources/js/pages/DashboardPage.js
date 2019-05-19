import React from 'react';
import Drawer from "../components/Drawer";

class DashboardPage extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    return(
      <div className="content">
        <div className="page-heading">
          <h1>Getting Started</h1>
        </div>
      </div>
    )
  }
}

export default DashboardPage;