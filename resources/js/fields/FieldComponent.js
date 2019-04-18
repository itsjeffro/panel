import React from 'react';
import PasswordField from './Password/PasswordField';
import BelongsToField from "./BelongsTo/BelongsToField";

const FieldComponent = (props) => {
  const components = {
    Password: PasswordField,
    BelongsTo: BelongsToField
  };

  const {
    field,
    value,
    handleInputChange
  } = props;

  const ComponentName = components[field.component];

  if (typeof ComponentName == 'undefined') {
    return <input
      className="form-control"
      type="text"
      name={field.column}
      value={value}
      onChange={handleInputChange}
    />;
  }

  return <ComponentName {...props} />
}

export default FieldComponent;