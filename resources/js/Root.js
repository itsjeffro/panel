import React from 'react';
import {BrowserRouter as Router, Switch, Route} from 'react-router-dom';

import DashboardPage from './pages/DashboardPage';
import ResourcePage from './pages/ResourcePage';

class Root extends React.Component {
  render() {
    return (
      <Router basename="/panel">
        <div>
          <Switch>
            <Route exact path="/" component={DashboardPage} />
            <Route exact path="/resources/:resource" component={ResourcePage} />
          </Switch>
        </div>
      </Router>
    )
  }
}

export default Root;