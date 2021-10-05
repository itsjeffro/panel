import React from 'react';
import {Link} from "react-router-dom";

const IndexField = (props) => {
  const {resource, resourceName, resourceId, field} = props;

  return (
    <span><Link to={`/resources/${resource}/${resourceId}`}>{resourceName}</Link></span>
  )
};

export default IndexField;
