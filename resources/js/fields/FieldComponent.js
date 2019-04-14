import React from 'react';
import PasswordField from './Password/PasswordField';

const FieldComponent = (props) => {
  const components = {
    Password: PasswordField
  };

  const {
    component,
    column,
    value,
    handleInputChange
  } = props;

  const ComponentName = components[component];

  if (typeof ComponentName == 'undefined') {
    return <input
      className="form-control"
      type="text"
      name={column}
      value={value}
      onChange={handleInputChange}
    />;
  }

  return <ComponentName
    column={column}
    value={value}
    handleInputChange={handleInputChange}
  />
}

export default FieldComponent;