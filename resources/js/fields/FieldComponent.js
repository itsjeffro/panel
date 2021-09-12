import React from 'react';
import PasswordField from './Password/PasswordField';
import BelongsToField from "./BelongsTo/BelongsToField";
import TextareaField from "./Textarea/TextareaField";
import DefaultComponent from "./DefaultComponent";

const FieldComponent = (props) => {
  const components = {
    Password: PasswordField,
    BelongsTo: BelongsToField,
    Textarea: TextareaField,
  };

  const {
    errors,
    field,
    handleInputChange,
    value
  } = props;

  const ComponentName = components[field.component] || DefaultComponent;
  const column = field.isRelationshipField ? field.foreignKey : field.column;
  const hasError = errors !== null && errors.hasOwnProperty(column);
  const messageError = errors !== null && errors.hasOwnProperty(column) ? errors[column][0] : '';

  return (
    <ComponentName
      {...props}
      column={column}
      hasError={hasError}
      messageError={messageError}
    />
  )
}

export default FieldComponent;