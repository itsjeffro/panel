import React from 'react';

const BelongsToField = (props) => {
  const {
    resource,
    field,
    handleInputChange,
    value
  } = props;

  const selected = value.id;

  return (
    <select
      className="form-control"
      name={field.column}
      onChange={e => handleInputChange(e)}
      value={selected}
    >
      <option value="">Choose {field.name}</option>
      {resource.relationships[field.column].map(relationship =>
        <option
          key={relationship.id}
          value={relationship.id}
        >{relationship[field.relation.title]}</option>
      )}
    </select>
  )
};

export default BelongsToField;