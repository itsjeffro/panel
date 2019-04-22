import React from 'react';
import PasswordField from './Password/PasswordField';
import BelongsToField from "./BelongsTo/BelongsToField";
import TextareaField from "./Textarea/TextareaField";

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

  const ComponentName = components[field.component];

  const column = field.isRelationshipField ? field.foreignKey : field.column;
  const hasError = errors !== null && errors.hasOwnProperty(column);
  const messageError = errors !== null && errors.hasOwnProperty(column) ? errors[column][0] : '';

  if (typeof ComponentName == 'undefined') {
    return (
      <span>
        <input
          className={'form-control' + (hasError ? ' is-invalid' : '')}
          type="text"
          name={column}
          value={value}
          onChange={handleInputChange}
          placeholder={field.name}
        />

        {hasError ? <div className="invalid-feedback">{messageError}</div> : ''}
      </span>
    )
  }

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