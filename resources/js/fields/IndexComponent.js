import React from 'react';
import BelongsToIndex from "./BelongsTo/BelongsToIndex";
import TextareaIndex from "./Textarea/TextareaIndex";

const IndexComponent = (props) => {
  const components = {
    BelongsTo: BelongsToIndex,
    Textarea: TextareaIndex,
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

export default IndexComponent;