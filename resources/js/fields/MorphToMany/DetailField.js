import React from 'react';

const DetailField = (props) => {
  const { field } = props;

  return (
    <span>
      { (field.value || []).join(', ') }
    </span>
  )
}

export default DetailField;
