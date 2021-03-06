import React from 'react';

const BelongsToField = (props) => {
  const {
    column,
    field,
    hasError,
    messageError,
    resource,
    handleInputChange,
    value
  } = props;

  return (
    <span>
      <select
        className={'form-control' + (hasError ? ' is-invalid' : '')}
        name={column}
        onChange={e => handleInputChange(e)}
        value={value}
      >
        <option value="">Choose {field.name}</option>
        {resource.relationships[field.column].map(relationship =>
          <option
            key={relationship.id}
            value={relationship.id}
          >{relationship[field.relation.title]}</option>
        )}
      </select>

      {hasError ? <div className="invalid-feedback">{messageError}</div> : ''}
    </span>
  )
};

export default BelongsToField;