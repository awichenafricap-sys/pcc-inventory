import './bootstrap';

import Alpine from 'alpinejs';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

window.Alpine = Alpine;

// Initialize Flatpickr with full month and day names
window.initDatePickers = function() {
    flatpickr('.date-picker', {
        dateFormat: 'l, F j, Y', // Full day name, full month name, day, year
        defaultDate: 'today', // Automatically set to current date
        monthSelectorType: 'static',
        locale: {
            firstDayOfWeek: 0,
            weekdays: {
                longhand: [
                    'Sunday',
                    'Monday',
                    'Tuesday',
                    'Wednesday',
                    'Thursday',
                    'Friday',
                    'Saturday'
                ],
                shorthand: [
                    'Sun',
                    'Mon',
                    'Tue',
                    'Wed',
                    'Thu',
                    'Fri',
                    'Sat'
                ]
            },
            months: {
                longhand: [
                    'January',
                    'February',
                    'March',
                    'April',
                    'May',
                    'June',
                    'July',
                    'August',
                    'September',
                    'October',
                    'November',
                    'December'
                ],
                shorthand: [
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
                    'Dec'
                ]
            }
        }
    });
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    window.initDatePickers();
});

Alpine.start();
