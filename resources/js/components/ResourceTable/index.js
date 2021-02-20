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
    <div className="table-card card">
      <div className="card-header">
        <div className="form-check form-check-inline">
          <input className="form-check-input" type="checkbox" />

          <div className="dropdown">
            <button className="btn pb-0 dropdown-toggle" onClick={ onDropdownBulkClick }>{''}</button>

            <div className={'dropdown-menu' + ( isDropdownBulkShown ? ' show' : '') }>
              <a className="dropdown-item" href="#">Bulk Delete</a>
            </div>
          </div>
        </div>
      </div>

      <table className="table mb-0">
        <thead>
        <tr>
          <th width="1%" className="border-top-0 text-right">
            {' '}
          </th>
          { resource.fields.map(field =>
            <th className="border-top-0" key={field.column}>{field.name}</th>
          ) }
          <th className="border-top-0 text-right">
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

      <div className="card-footer card-pagination">
        <Pagination
          total={ resource.model_data.total }
          per_page={ resource.model_data.per_page }
          current_page={ resource.model_data.current_page }
          handlePageClick={ onPageClick }
        />
      </div>
    </div>
  )
}

export default ResourceTable;
