import moment from 'moment';
import Vue from 'vue';

Vue.filter('capitalize', function (value) {
    if (!value) return '';
    value = value.toString();
    const arr = value.split(' ');
    const capitalized_array = [];
    arr.forEach((word) => {
        const capitalized = word.charAt(0).toUpperCase() + word.slice(1);
        capitalized_array.push(capitalized);
    });
    return capitalized_array.join(' ');
});

Vue.filter('title', function (value, replacer = '_') {
    if (!value) return '';
    value = value.toString();

    const arr = value.split(replacer);
    const capitalized_array = [];
    arr.forEach((word) => {
        const capitalized = word.charAt(0).toUpperCase() + word.slice(1);
        capitalized_array.push(capitalized);
    });
    return capitalized_array.join(' ');
});

Vue.filter('truncate', function (value, limit) {
    return value.substring(0, limit);
});

Vue.filter('tailing', function (value, tail) {
    return value + tail;
});

Vue.filter('time', function (value, is24HrFormat = false) {
    if (value) {
        const date = new Date(Date.parse(value));
        let hours = date.getHours();
        const min = (date.getMinutes() < 10 ? '0' : '') + date.getMinutes();
        if (!is24HrFormat) {
            const time = hours > 12 ? 'AM' : 'PM';
            hours = hours % 12 || 12;
            return `${hours}:${min} ${time}`;
        }
        return `${hours}:${min}`;
    }
});

Vue.filter('date', function (value, fullDate = false) {
    value = String(value);
    const date = value.slice(8, 10).trim();
    const month = value.slice(4, 7).trim();
    const year = value.slice(11, 15);

    if (!fullDate) return `${date} ${month}`;
    else return `${date} ${month} ${year}`;
});

Vue.filter('month', function (val, showYear = true) {
    val = String(val);

    const regx = /\w+\s(\w+)\s\d+\s(\d+)./;
    if (!showYear) {
        return regx.exec(val)[1];
    } else {
        return `${regx.exec(val)[1]} ${regx.exec(val)[2]}`;
    }

});



Vue.filter('csv', function (value) {
    return value.join(', ');
});

Vue.filter('filter_tags', function (value) {
    return value.replace(/<\/?[^>]+(>|$)/g, '');
});

Vue.filter('k_formatter', function (num) {
    return num > 999 ? `${(num / 1000).toFixed(1)}k` : num;
});

Vue.filter('utc_to_local', date => {
    if (!date) return "";
    var utc = moment.utc(date).toDate();
    return moment(utc).local().format("MMM DD, YYYY hh:mm:A");
});

Vue.filter('dateToFromNowDaily', date => {

    // get from-now for this date
    var fromNow = moment.utc(date);

    // ensure the date is displayed with today and yesterday
    return moment.utc(date).calendar(null, {
        // when the date is closer, specify custom values
        lastWeek: '[Last] dddd',
        lastDay: '[Yesterday]',
        sameDay: '[Today]',
        nextDay: '[Tomorrow]',
        nextWeek: 'dddd',
        // when the date is further away, use from-now functionality             
        sameElse: function (now) {
            if (now.isSame(date, 'year')) {
                return "[" + fromNow.format("MMM DD") + "]";
            } else {
                return "[" + fromNow.format("MMM DD, YYYY") + "]";
            }
        }
    });
});

Vue.filter('dateColor', (value, date) => {
    if (value == 'Today' || value == 'Tomorrow') {
        return 'color-success';
    }
    if (value == 'Yesterday') {
        return 'color-danger';
    }
    if (moment.utc(date) < moment.now()) {
        return 'color-danger';
    }
    return;
});

/**
 * Format the given money value.
 *
 * Source: https://github.com/vuejs/vue/blob/1.0/src/filters/index.js#L70
 */
Vue.filter('currency', value => {
    value = parseFloat(value);

    if (!isFinite(value) || (!value && value !== 0)) {
        return '';
    }

    var stringified = Math.abs(value).toFixed(2);

    var _int = stringified.slice(0, -1 - 2);

    var i = _int.length % 3;

    var head = i > 0
        ? (_int.slice(0, i) + (_int.length > 3 ? ',' : ''))
        : '';

    var _float = stringified.slice(-1 - 2);

    var sign = value < 0 ? '-' : '';

    return sign + '$' + head +
        _int.slice(i).replace(/(\d{3})(?=\d)/g, '$1,') +
        _float;
});

/**
 * Format the given date.
 */
 Vue.filter('spark-date', value => {
    return moment.utc(value).local().format('MMMM Do, YYYY')
});
