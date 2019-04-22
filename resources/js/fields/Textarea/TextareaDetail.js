import React from 'react';

const TextareaDetail = (props) => {
  const {
    model,
    field,
  } = props;

  return (
    <span>{model[field.column]}</span>
  )

};

export default TextareaDetail;