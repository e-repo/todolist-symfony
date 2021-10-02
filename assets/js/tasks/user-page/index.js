// Import jQuery
import $ from 'jquery';

// Import date-picker js
import 'bootstrap-datepicker';

// Import date-picker styles
import 'bootstrap-datepicker/dist/css/bootstrap-datepicker.css';

import DatePickerFilter from "../../components/DatePickerFilter";

// Init DatePicker
new DatePickerFilter([
    {name: 'js-task-date'}
]);