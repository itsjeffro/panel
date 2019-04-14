import React from 'react';
import BelongsToIndex from "./BelongsTo/BelongsToIndex";

const IndexComponent = (props) => {
  const components = {
    BelongsTo: BelongsToIndex
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