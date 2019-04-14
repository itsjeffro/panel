import React from 'react';

const Pagination = (props) => {
  const {
    total,
    per_page,
    current_page
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
        <li className="page-item">
          <a className="page-link" href="#" aria-label="Previous">
            <span aria-hidden="true">&laquo;</span>
            <span className="sr-only">Previous</span>
          </a>
        </li>

        {pages.map(page =>
          <li className="page-item"><a className="page-link" href="#">{page}</a></li>
        )}

        <li className="page-item">
          <a className="page-link" href="#" aria-label="Next">
            <span aria-hidden="true">&raquo;</span>
            <span className="sr-only">Next</span>
          </a>
        </li>
      </ul>
    </nav>
  )
};

export default Pagination;