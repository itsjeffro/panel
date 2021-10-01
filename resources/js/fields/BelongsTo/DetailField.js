import React from 'react';
import {Link} from "react-router-dom";

const DetailField = (props) => {
  const { resource, resourceName, resourceId, field } = props;

  return (
    <span><Link to={ `/resources/${resourceName}/${resourceId}` }>{ field.value }</Link></span>
  )
}

export default DetailField;
