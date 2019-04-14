import React from 'react';

const Pagination = (props) => {
  const {
    total,
    per_page,
    current_page,
    handlePageClick
  } = props;

  const total_pages = Math.ceil(total / per_page);

  let pages = [];

  for (let page = 1; page <= total_pages; page++) {
    pages.push(page);
  }

  return (
    <nav>
      <div className="pt-2 pr-3 float-right">
        Total: {total}
      </div>
      <ul className="pagination">
        <li className={'page-item' + (current_page <= 1 ? ' disabled' : '')}>
          <a className="page-link" aria-label="Previous" href="#" onClick={e => handlePageClick(e, 1)}>
            <span aria-hidden="true">&laquo;</span>
            <span className="sr-only">Previous</span>
          </a>
        </li>

        {pages.map(page =>
          <li className={'page-item' + (page === current_page ? ' active' : '')} key={'page-' + page}>
            <a className="page-link" href="#" onClick={e => handlePageClick(e, page)}>{page}</a>
          </li>
        )}

        <li className={'page-item' + (current_page >= total_pages ? ' disabled' : '')}>
          <a className="page-link" aria-label="Next" href="#" onClick={e => handlePageClick(e, total_pages)}>
            <span aria-hidden="true">&raquo;</span>
            <span className="sr-only">Next</span>
          </a>
        </li>
      </ul>
    </nav>
  )
};

export default Pagination;