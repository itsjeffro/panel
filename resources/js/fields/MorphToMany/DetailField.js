import React from 'react';

const DetailField = (props) => {
  const { resource, resourceId, resourceName, field } = props;

  return (
    <span>
      { (resourceName || []).join(', ') }
    </span>
  )
}

export default DetailField;
