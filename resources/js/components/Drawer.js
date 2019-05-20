import React from 'react';
import {Link} from "react-router-dom";

const Drawer = (props) => {
  const resources = window.panel.resources;

  return (
    <div className="drawer">
      <h2>Panel</h2>
      <ul>
        <li>
          <Link to="/"><span className="typcn typcn-home-outline" />Dashboard</Link>
        </li>
        <li>
          <a href="#"><span className="typcn typcn-th-large-outline" />Resources</a>
          <ul>
            {resources.map(resource =>
              <li key={resource.slug}>
                <Link to={'/resources/' + resource.slug}>{resource.name}</Link>
              </li>
            )}
          </ul>
        </li>
      </ul>
    </div>
  )
};

export default Drawer;