var Vue = require('vue');
var Vuex = require('vuex');

Vue.use(Vuex);

Vue.config.debug = true;

// modules
import users from './modules/users.js';
import projects from './modules/projects.js';
import teams from './modules/teams.js';

export default new Vuex.Store({

    modules: {
        users,
        projects,
        teams
    },
});

