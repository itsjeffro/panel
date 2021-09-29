import React from 'react';

const IndexField = (props) => {
  const {
    model,
    field,
  } = props;

  return (
    <span>{model[field.column]}</span>
  )
};

export default IndexField;