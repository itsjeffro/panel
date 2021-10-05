import React from 'react';
import PasswordFormField from './Password/FormField';
import BelongsToField from './BelongsTo/FormField';
import MorphToManyField from './MorphToMany/FormField';
import TextareaField from './Textarea/FormField';
import TextField from "./Text/FormField";
import DateTimeField from "./DateTime/FormField";

const FormFieldComponent = (props) => {
  const components = {
    Password: PasswordFormField,
    BelongsTo: BelongsToField,
    MorphToMany: MorphToManyField,
    Textarea: TextareaField,
    DateTime: DateTimeField,
  };

  const { component, errors, field } = props;

  const ComponentName = components[component] || TextField;
  const column = field.attribute;
  const hasError = errors !== null && errors.hasOwnProperty(column);
  const messageError = errors !== null && errors.hasOwnProperty(column) ? errors[column][0] : '';
  const value = props.value || '';

  return (
    <ComponentName
      { ...props }
      column={ column }
      value={ value }
      hasError={ hasError }
      messageError={ messageError }
    />
  )
}

export default FormFieldComponent;