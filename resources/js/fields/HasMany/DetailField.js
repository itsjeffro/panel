import React from 'react';
import ResourceTable from "../../components/ResourceTable";

const DetailField = (props) => {
  const { relationship, resource, resourceName, resourceId, field } = props;

  return (
    <div className="mt-5">
      <ResourceTable
        resourceUri={ field.attribute }
        uriQueries={{
          resource: resource,
          resourceId: resourceId,
          relationship: field.attribute
        }}
      />
    </div>
  )
}

export default DetailField;
