import React from 'react';
import {BrowserRouter as Router, Switch, Route} from 'react-router-dom';

import HomePage from './pages/HomePage';
import DiscussionPage from './pages/DiscussionPage';

class Root extends React.Component {
  render() {
    return (
      <Router basename="/forum">
        <div>
          <Switch>
            <Route exact path="/" component={HomePage} />
            <Route exact path="/channels/:channel/discussions/:discussion" component={DiscussionPage} />
          </Switch>
        </div>
      </Router>
    )
  }
}

export default Root;