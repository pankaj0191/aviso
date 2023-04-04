// system.js
// main module
//----------------------------------

import Team from "../../models/team";

import { TEAM } from "../mutation-types.js";

/*
 * state
 *
 */

const state = {
    teams: [],
    teamLoading: false
};

/*
* module getters
*/

const getters = {
    teams: state => state.teams,
    teamLoading: state => state.teamLoading
};

/*
* module mutations
*/

const mutations = {
    /**
     * Always called on initial page load, which occurs in DashboardLayout
     * broadcast data-ready event
     */
    [TEAM](state, data) {
        if (data) {
            state.teams = data;
        }
    },
    UPDATE_LOADING(state, data) {
        state.teamLoading = data;
    }

};

/*
* module actions
*/

const actions = {

    loadTeams({ state, commit }) {
        commit('UPDATE_LOADING', true);
        return Team.teams().then(teams => {
            commit('UPDATE_LOADING', false);
            commit(TEAM, teams);
        });
    },
};

export default {
    state,
    getters,
    mutations,
    actions
};
