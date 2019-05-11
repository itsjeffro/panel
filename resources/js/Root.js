import React from 'react';
import {BrowserRouter as Router, Switch, Route} from 'react-router-dom';

import DashboardPage from './pages/DashboardPage';
import ResourceIndexPage from './pages/ResourceIndexPage';
import ResourceCreatePage from './pages/ResourceCreatePage';
import ResourceEditPage from './pages/ResourceEditPage';
import ResourceViewPage from './pages/ResourceViewPage';

class Root extends React.Component {
  render() {
    return (
      <Router basename="/panel">
        <>
          <Switch>
            <Route exact path="/" component={DashboardPage} />
            <Route exact path="/resources/:resource" component={ResourceIndexPage} />
            <Route exact path="/resources/:resource/create" component={ResourceCreatePage} />
            <Route exact path="/resources/:resource/:id" component={ResourceViewPage} />
            <Route exact path="/resources/:resource/:id/edit" component={ResourceEditPage} />
          </Switch>
        </>
      </Router>
    )
  }
}

export default Root;