import React from 'react';
import Drawer from "../components/Drawer";

class DashboardPage extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    return(
      <div className="wrapper">
        <Drawer/>

        <main className="main-content">
          <div className="content">
            <div className="page-heading">
              <h1>Getting Started</h1>
            </div>
          </div>
        </main>
      </div>
    )
  }
}

export default DashboardPage;