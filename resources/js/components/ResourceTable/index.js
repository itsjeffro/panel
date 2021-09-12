import React from 'react';
import IndexComponent from "../../fields/IndexComponent";
import {Link} from "react-router-dom";
import Pagination from "../Pagination";

const ResourceTable = (props) => {
  const {
    onPageClick,
    onDropdownBulkClick,
    onDeleteClick,
    isDropdownBulkShown,
    resource,
    params,
  } = props;

  return (
    <>
      <div className="form-check form-check-inline">
        <div className="dropdown">
          <button className="btn btn-secondary dropdown-toggle" onClick={ onDropdownBulkClick }>Actions</button>

          <div className={'dropdown-menu' + ( isDropdownBulkShown ? ' show' : '') }>
            <a className="dropdown-item" href="#">Bulk Delete</a>
          </div>
        </div>
      </div>

      <table className="table">
        <thead>
        <tr>
          <th width="1%">
            <input type="checkbox" />
          </th>
          { resource.fields.map(field =>
            <th key={field.column}>{field.name}</th>
          ) }
          <th className="text-right">
            {' '}
          </th>
        </tr>
        </thead>

        <tbody>
        {( resource.model_data.data).map((model) =>
          <tr key={ model.id }>
            <td width="1%">
              <div className="form-check form-check-inline">
                <input className="form-check-input" type="checkbox" />
              </div>
            </td>
            { resource.fields.map((field) =>
              <td key={ model.id + '-' + field.column }>
                <IndexComponent
                  component={ field.component }
                  model={ model }
                  field={ field }
                />
              </td>
            ) }
            <td className="text-right">
              <Link className="btn btn-link" to={'/resources/' + params.resource + '/' + model.id}><span className="typcn typcn-eye-outline" /></Link>{' '}
              <Link className="btn btn-link" to={'/resources/' + params.resource + '/' + model.id + '/edit'}><span className="typcn typcn-edit" /></Link>{' '}
              <button
                className="btn btn-link"
                onClick={ (e) => onDeleteClick(e, params.resource, model.id) }
              ><span className="typcn typcn-trash" /></button>
            </td>
          </tr>
        ) }
        </tbody>
      </table>

      <Pagination
        total={ resource.model_data.total }
        per_page={ resource.model_data.per_page }
        current_page={ resource.model_data.current_page }
        handlePageClick={ onPageClick }
      />
    </>
  )
}

export default ResourceTable;
