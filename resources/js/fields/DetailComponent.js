import React from 'react';
import BelongsToField from "./BelongsTo/DetailField";
import MorphToManyDetail from "./MorphToMany/DetailField";
import TextareaField from "./Textarea/DetailField";
import DateTimeField from "./DateTime/DetailField";
import HasManyField from "./HasMany/DetailField";

const DetailComponent = (props) => {
  const components = {
    BelongsTo: BelongsToField,
    MorphToMany: MorphToManyDetail,
    Textarea: TextareaField,
    DateTime: DateTimeField,
    HasMany: HasManyField,
  };

  const {
    component,
    field
  } = props;

  const ComponentName = components[component];

  if (typeof ComponentName == 'undefined') {
    return <span>{ field.value }</span>;
  }

  return <ComponentName {...props} />
}

export default DetailComponent;