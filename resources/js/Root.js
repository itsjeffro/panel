import React from 'react';
import {BrowserRouter as Router, Switch, Route} from 'react-router-dom';

import DashboardPage from './pages/DashboardPage';
import ResourceIndexPage from './pages/ResourceIndexPage';
import ResourceCreatePage from './pages/ResourceCreatePage';
import ResourceEditPage from './pages/ResourceEditPage';
import ResourceViewPage from './pages/ResourceViewPage';
import Layout from "./containers/Layout";

class Root extends React.Component {
  render() {
    return (
      <Router basename="/panel">
        <Layout>
          <Switch>
            <Route exact path="/" component={DashboardPage} />
            <Route exact path="/resources/:resource" component={ResourceIndexPage} />
            <Route exact path="/resources/:resource/create" component={ResourceCreatePage} />
            <Route exact path="/resources/:resource/:id" component={ResourceViewPage} />
            <Route exact path="/resources/:resource/:id/edit" component={ResourceEditPage} />
          </Switch>
        </Layout>
      </Router>
    )
  }
}

export default Root;