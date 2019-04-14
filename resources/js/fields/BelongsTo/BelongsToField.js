import React from 'react';

const BelongsToField = (props) => {
  const {
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
        <option value="">Select {column}</option>
      </select>
    </span>
  )
};

export default BelongsToField;