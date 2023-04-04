
import Ls from './services/ls';
import * as Toastr from 'toastr';
import 'toastr/build/toastr.css';
window.toastr = Toastr;
window._ = require('lodash');
window.URI = require('urijs');
window.moment = require('moment');
window.Promise = require('promise');

import Card from 'card';
window.card = Card;

/*
 * Load various JavaScript modules that assist Spark.
 */

window.__ = (key, replace) => {
    // var translation = Spark.translations[key] ? Spark.translations[key] : key;
    var translation = key;

    _.forEach(replace, (value, key) => {
        translation = translation.replace(':'+key, value);
    });
    
    return translation;
};


/*
 * Define Moment locales
 */
window.moment.defineLocale('en-short', {
    parentLocale: 'en',
    relativeTime : {
        future: "in %s",
        past:   "%s",
        s:  "1s",
        m:  "1m",
        mm: "%dm",
        h:  "1h",
        hh: "%dh",
        d:  "1d",
        dd: "%dd",
        M:  "1 month ago",
        MM: "%d months ago",
        y:  "1y",
        yy: "%dy"
    }
});
window.moment.locale('en');



/*
 * Load jQuery and Bootstrap jQuery, used for front-end interaction.
 */
if (window.$ === undefined || window.jQuery === undefined) {
    window.$ = window.jQuery = require('jquery');
}

require('bootstrap');


/**
 * Load Vue if this application is using Vue as its framework.
 */
/*
 * Load Vue & Vue-Resource.
 *
 * Vue is the JavaScript framework used by Spark.
 */
if (window.Vue === undefined) {
    window.Vue = require('vue');

    window.Bus = new Vue();
}



/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.$ = window.jQuery = require('jquery');
    window.Popper = require('popper.js').default;
    // require('bootstrap');
} catch (e) { }

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// axios.interceptors.request.use(config => {
//     app.$Progress.start(); // for every request start the progress
//     return config;
// });

// axios.interceptors.response.use(response => {
//     app.$Progress.finish(); // finish when a response is received
//     return response;
// });

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');
window.spacePrefix = process.env.MIX_PUSHER_APP_SPACES_PREFIX

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true
});

/**
 * Intercept the incoming responses.
 *
 * Handle any unexpected HTTP errors and pop up modals, etc.
 */
window.axios.interceptors.response.use(function (response) {
    return response;
}, function (error) {
    switch (error.response.status) {
        case 401:
            window.axios.get('/logout');
            $('#modal-session-expired').modal('show');
            break;

        case 402:
            window.location = '/settings#/subscription';
            break;
    }

    return Promise.reject(error);
});
