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
  hours = hours === 0 ? 12 : hours;

  let minutes = date.getMinutes();
  let meridiem = date.getHours() >= 12 ? 'PM' : 'AM';
  let timezoneName = Intl.DateTimeFormat().resolvedOptions().timeZone;

  const dateTime = [
    day + ' ' + months[month] + ' ' + year,
    hours + ':' + minutes + ' ' + meridiem
  ];

  return (
    <span title={ field.value }>
      { `${dateTime.join(' - ')} (${timezoneName})`  }
    </span>
  )
};

export default DetailField;