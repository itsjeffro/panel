import React from 'react';
import BelongsToDetail from "./BelongsTo/BelongsToDetail";
import TextareaDetail from "./Textarea/TextareaDetail";

const DetailComponent = (props) => {
  const components = {
    BelongsTo: BelongsToDetail,
    Textarea: TextareaDetail,
  };

  const {
    component,
    model,
    field
  } = props;

  const ComponentName = components[component];

  if (typeof ComponentName == 'undefined') {
    return <span>{model[field.column]}</span>;
  }

  return <ComponentName {...props} />
}

export default DetailComponent;