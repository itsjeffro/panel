import React from 'react';

const TextareaIndex = (props) => {
  const {
    model,
    field,
  } = props;

  return (
    <span>{model[field.column]}</span>
  )
};

export default TextareaIndex;