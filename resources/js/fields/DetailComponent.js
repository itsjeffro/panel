import React from 'react';
import BelongsToField from "./BelongsTo/DetailField";
import MorphToManyDetail from "./MorphToMany/DetailField";
import TextareaField from "./Textarea/DetailField";

const DetailComponent = (props) => {
  const components = {
    BelongsTo: BelongsToField,
    MorphToMany: MorphToManyDetail,
    Textarea: TextareaField,
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