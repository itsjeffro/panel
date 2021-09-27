import React from 'react';
import PasswordFormField from './Password/FormField';
import BelongsToField from './BelongsTo/BelongsToField';
import MorphToMany from './MorphToMany/FormField';
import TextareaField from './Textarea/TextareaField';
import TextFormField from "./Text/FormField";

const FormFieldComponent = (props) => {
  const components = {
    Password: PasswordFormField,
    BelongsTo: BelongsToField,
    MorphToMany: MorphToMany,
    Textarea: TextareaField,
  };

  const { errors, field } = props;

  const ComponentName = components[field.component] || TextFormField;
  const column = field.column;
  const hasError = errors !== null && errors.hasOwnProperty(column);
  const messageError = errors !== null && errors.hasOwnProperty(column) ? errors[column][0] : '';
  const value = props.value || '';

  return (
    <ComponentName
      {...props}
      column={column}
      value={ value }
      hasError={hasError}
      messageError={messageError}
    />
  )
}

export default FormFieldComponent;