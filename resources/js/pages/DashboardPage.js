import React from 'react';
import Drawer from "../components/Drawer";
import {IconBrandGithub} from "@tabler/icons";

class DashboardPage extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    return(
      <div className="content">
        <div className="container">
          <div className="page-heading">
            <h2>Getting Started</h2>

            <div className="card">
              <div className="card-body">
                <h5 className="card-title">Documentation</h5>
                <p>Additional documentation can be found in the <code>/docs</code> directory.</p>

                <a
                  href="https://github.com/itsjeffro/panel"
                  title="Github"
                ><IconBrandGithub style={{ verticalAlign: 'text-bottom' }} height={18} width={18}/> itsjeffro/panel</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    )
  }
}

export default DashboardPage;