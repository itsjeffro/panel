import React from 'react';
import {Link} from "react-router-dom";
import { IconHome, IconTriangleSquareCircle } from '@tabler/icons';

const Drawer = (props) => {
  const resources = window.panel.resources;

  return (
    <div className="drawer">
      <h2>Panel</h2>
      <ul>
        <li>
          <Link to="/"><IconHome height={19} width={19} /> Dashboard</Link>
        </li>
        <li>
          <a href="#"><IconTriangleSquareCircle height={19} width={19} /> Resources</a>
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