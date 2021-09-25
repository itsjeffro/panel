import React from 'react';

const IndexField = (props) => {
  const {
    model,
    field
  } = props;

  return (
    <span>
      { model[field.column].map((row) => {
        return row[field.relation.title]
      }).join(', ')}
    </span>
  )
};

export default IndexField;