import React from 'react';

const BelongsToIndex = (props) => {
  const {
    model,
    field
  } = props;

  return (
    <span>{model[field.column][field.relation.title]}</span>
  )
};

export default BelongsToIndex;