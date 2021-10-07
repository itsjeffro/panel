class CheckBox {
  constructor(items) {
    this.items = items;
  }

  /**
   * Set and return checked rows.
   */
  checkedRows(rows, indexKey) {
    // If there are no checked rows and an end indexKey is undefined, then check all rows.
    if (typeof indexKey === 'undefined' && !this.hasCheckedRows()) {
      return rows;
    }

    // If there are checked rows and an end index is provided, then remove all checked rows.
    if (typeof indexKey === 'undefined' && this.hasCheckedRows()) {
      return {};
    }

    // Uncheck a single row if it already exists in the items property.
    if (this.getCheckedRows().hasOwnProperty(indexKey)) {
      return this.removeCheckedRow(indexKey);
    }

    // Add row to the items list with the index as the key and resourceId as the value.
    return {
      ...this.getCheckedRows(),
      [indexKey]: rows[indexKey],
    };
  }

  /**
   * Determines if any rows are checked.
   *
   * @returns {boolean}
   */
  hasCheckedRows() {
    return Object.keys(this.items).length > 0;
  }

  /**
   * Returns checked rows.
   *
   * @returns {object}
   */
   getCheckedRows() {
    return this.items;
  }

  /**
   * Remove checked row by its index key from the items list.
   *
   * @param {string} indexKey
   * @returns {object}
   */
  removeCheckedRow(indexKey) {
    const filteredRows = Object.keys(this.getCheckedRows())
      .filter((index) => {
        return Number(index) !== Number(indexKey);
      });

    return filteredRows.reduce((object, index) => {
      object[index] = this.getCheckedRows()[index];

      return object;
    }, {});
  }
}

export default CheckBox;
