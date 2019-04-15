import React from 'react';

const BelongsToDetail = (props) => {
  const {
    model,
    field
  } = props;

  return (
    <span>{model[field.column][field.relation.title]}</span>
  )
};

export default BelongsToDetail;