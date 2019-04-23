import React from 'react';
import {Link} from "react-router-dom";

const Drawer = (props) => {
  const resources = window.panel.resources;

  return (
    <div className="drawer">
      <h3>Resources</h3>
      <ul>
        {resources.map(resource =>
          <li key={resource.slug}>
          <Link to={'/resources/' + resource.slug}>{resource.name}</Link>
          </li>
        )}
      </ul>
    </div>
  )
};

export default Drawer;