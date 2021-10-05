import React from 'react';

const DetailField = (props) => {
  const { field } = props;

  const months = [
    'Jan',
    'Feb',
    'Mar',
    'Apr',
    'May',
    'Jun',
    'Jul',
    'Aug',
    'Sep',
    'Oct',
    'Nov',
    'Dec',
  ];

  let date = new Date(field.value);
  let month = date.getMonth();
  let day = date.getDate();
  let year = date.getFullYear();
  let hours = date.getHours() % 12;
  let minutes = date.getMinutes();
  let meridiem = date.getHours() >= 12 ? 'PM' : 'AM';

  return (
    <span title={ field.value }>
      { day + ' ' + months[month] + ' ' + year + ' - ' + hours + ':' + minutes + ' ' + meridiem }
    </span>
  )
};

export default DetailField;