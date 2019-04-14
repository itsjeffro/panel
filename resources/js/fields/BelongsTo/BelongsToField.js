import React from 'react';

const BelongsToField = (props) => {
  const {
    name,
    column,
    handleInputChange
  } = props;

  return (
    <span>
      <select
        className="form-control"
        name={column}
        onChange={e => handleInputChange(e)}
      >
        <option value="">Select {name}</option>
      </select>
    </span>
  )
};

export default BelongsToField;