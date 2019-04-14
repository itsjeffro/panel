import React from 'react';

const BelongsToIndex = (props) => {
  const {
    model,
    field
  } = props;

  return (
    <span>{model[field.column]}</span>
  )
};

export default BelongsToIndex;