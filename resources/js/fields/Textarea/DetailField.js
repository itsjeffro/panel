import React from 'react';

const DetailField = (props) => {
  const {
    model,
    field,
  } = props;

  return (
    <span>{model[field.column]}</span>
  )

};

export default DetailField;