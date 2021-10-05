import React from 'react';
import BelongsToField from "./BelongsTo/IndexField";
import HasManyField from "./HasMany/IndexField";
import MorphToManyField from "./MorphToMany/IndexField";
import TextareaField from "./Textarea/IndexField";
import TextField from "./Text/IndexField";

const IndexComponent = (props) => {
  const components = {
    BelongsTo: BelongsToField,
    MorphToMany: MorphToManyField,
    Textarea: TextareaField,
    HasMany: HasManyField,
  };

  const { component, field } = props;
  const ComponentName = components[component];

  if (typeof ComponentName == 'undefined') {
    return <TextField field={ field } />
  }

  return <ComponentName {...props} />
}

export default IndexComponent;