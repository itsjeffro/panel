import React from 'react';
import {Link} from "react-router-dom";

const BelongsToDetail = (props) => {
  const {
    model,
    field
  } = props;

  const value = model ? model[field.column][field.relation.title] : null;
  const id = model ? model[field.relation.foreign_key] : null;
  const table = field.relation.table;

  return (
    <span><Link to={ `/resources/${table}/${id}` }>{ value }</Link></span>
  )
}

export default BelongsToDetail;
