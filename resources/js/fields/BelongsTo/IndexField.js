import React from 'react';

const IndexField = (props) => {
  const { field } = props;

  return (
    <span>{ JSON.stringify(field.value) }</span>
  )
};

export default IndexField;