import React from 'react';

const IndexField = (props) => {
  const { field } = props;

  return (
    <span>
      { field.value.join(', ') }
    </span>
  )
};

export default IndexField;