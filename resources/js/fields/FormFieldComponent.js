import React from 'react';
import PasswordField from './Password/PasswordField';
import BelongsToField from './BelongsTo/BelongsToField';
import MorphToMany from './MorphToMany/FormField';
import TextareaField from './Textarea/TextareaField';
import FormField from "./Text/FormField";

const FormFieldComponent = (props) => {
  const components = {
    Password: PasswordField,
    BelongsTo: BelongsToField,
    MorphToMany: MorphToMany,
    Textarea: TextareaField,
  };

  const { errors, field } = props;

  const ComponentName = components[field.component] || FormField;
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