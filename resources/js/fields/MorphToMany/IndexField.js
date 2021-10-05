import React from 'react';

const IndexField = (props) => {
  const { resource, resourceId, resourceName, field } = props;

  return (
    <span>
      { (resourceName || []).join(', ') }
    </span>
  )
};

export default IndexField;