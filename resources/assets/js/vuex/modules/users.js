// system.js
// main module
//----------------------------------

import User from "../../models/user";

import { LOADING_PENDING, LOADING_COMPLETED, BOOTSTRAP, PROFILE, UPDATE_PROFILE, UPDATE_REVIEW_PROJECT_NOTIFY, USER_STORAGE } from "../mutation-types.js";

/*
 * state
 *
 */

const state = {
    stateLoading: true,
    changePlanStatus: false,
    spark: null,
    nova: null,
    support: null,
    user: {},
    storage_states: {},
    profile: {},
    userProfile: {},
    total_projects: '',
    hours_worked: '',
    status_profile: true,
    isFreelancer: '',
    isSubscribed: '',
    projects_listing_type: '',
    current_role: '',
    currentRole: '',
    unread_comments: '',
    ownedTeam: {},
    hasTeams: {},
    teamMembers: {},
    exitingEmails: [],
    pendingEmails: [],
    invitations: [],
    membersLimit: '',
    storage_capacity: '',
    clientEmails: [],
    clientNames: [],
    projectCompanies: [],
    activeSubscription: {},
    activePlan: {},
    ownedTeams: {},
    paymentMethods: [],
    defaultMethod: '',
    windowWidth: null,
    allowedNotify: {}
};

/*
* module getters
*/

const getters = {
    windowBreakPoint: state => {

        // This should be same as tailwind. So, it stays in sync with tailwind utility classes
        if (state.windowWidth >= 1200) return "xl";
        else if (state.windowWidth >= 992) return "lg";
        else if (state.windowWidth >= 768) return "md";
        else if (state.windowWidth >= 576) return "sm";
        else return "xs";
    },
    windowWidth: state => state.windowWidth,
    stateLoading: state => state.stateLoading,
    changePlanStatus: state => state.changePlanStatus,
    spark: state => state.spark,
    profile: state => state.profile,
    userProfile: state => state.userProfile,
    totalProjects: state => state.total_projects,
    hoursWorked: state => state.hours_worked,
    statusProfile: state => state.status_profile,
    nova: state => state.nova,
    storage_states: state => state.storage_states,
    support: state => state.support,
    user: state => state.user,
    isFreelancer: state => state.isFreelancer,
    isSubscribed: state => state.isSubscribed,
    current_role: state => state.current_role,
    storage_capacity: state => state.storage_capacity,
    isBothRole: state => (state.currentRole == 'Agency' || state.currentRole == 'Freelancer'),
    currentRole: state => state.currentRole,
    projects_listing_type: state => state.projects_listing_type,
    unread_comments: state => state.unread_comments,
    ownedTeam: state => state.ownedTeam,
    hasTeams: state => state.hasTeams,
    exitingEmails: state => state.exitingEmails,
    teamMembers: state => state.teamMembers,
    pendingEmails: state => state.pendingEmails,
    invitations: state => state.invitations,
    membersLimit: state => state.membersLimit,
    clientEmails: state => state.clientEmails,
    clientNames: state => state.clientNames,
    projectCompanies: state => state.projectCompanies,
    activeSubscription: state => state.activeSubscription,
    activePlan: state => state.activePlan,
    ownedTeams: state => state.ownedTeams,
    paymentMethods: state => state.paymentMethods,
    defaultMethod: state => state.defaultMethod,
    allowedNotify: state => state.allowedNotify,
    reviewProjectNotify: state => state.allowedNotify.review_project ? true : false,
};

/*
* module mutations
*/

const mutations = {
    // Loading
    [LOADING_PENDING](state) {
        state.stateLoading = true;
    },
    [LOADING_COMPLETED](state) {
        state.stateLoading = false;
    },
    /**
     * Always called on initial page load, which occurs in DashboardLayout
     * broadcast data-ready event
     */
    [BOOTSTRAP](state, data) {
        localStorage.setItem('user', JSON.stringify(data.user));
        state.spark = data.developer;
        state.nova = data.nova;
        state.support = data.support;
        state.user = data.user;
        state.currentRole = data.currentRole;
        state.isFreelancer = data.isFreelancer;
        state.isSubscribed = data.isSubscribed;
        state.projects_listing_type = data.user.projects_listing_type;
        state.unread_comments = data.unread_comments;
        state.ownedTeam = data.ownedTeam;
        state.hasTeams = data.hasTeams;
        state.teamMembers = data.exitingMembers;
        state.allowedNotify = data.allowedNotify;
        data.exitingMembers.forEach(function (user) {
            state.exitingEmails.push(user.email);
        });
        data.teamInvitations.forEach(function (invitation) {
            state.pendingEmails.push(invitation.email);
            state.invitations.push(invitation);
        });
        state.membersLimit = data.currentPlan && data.currentPlan.length
            ? data.currentPlan.teams_members_count
            : 0;
        state.storage_capacity = data.currentPlan ? data.currentPlan.storage_capacity : 0;
    },
    /**
     * Call user Profile
     * broadcast data-ready event
     */
    [USER_STORAGE](state, data) {
        state.storage_states = data;
    },
    /**
     * Call user Profile
     * broadcast data-ready event
     */
    [PROFILE](state, data) {
        state.profile = data.profile;
        state.userProfile = data.userData;
        state.total_projects = data.total_projects;
        state.hours_worked = data.hours;
        state.status_profile = data.status_profile;
    },
    [UPDATE_PROFILE](state, data) {
        state.profile = data.profile;
        state.userProfile = data.userData;
    },
    [UPDATE_REVIEW_PROJECT_NOTIFY](state, data) {
        state.allowedNotify = data.email_notifications;
        state.reviewProjectNotify = data.email_notifications.review_project;
    },
    UPDATE_WINDOW_WIDTH(state, width) { state.windowWidth = width; },
    addInvitation(state, invitation) {
        invitation.forEach(function (invite) {
            state.pendingEmails.push(invite.email);
            state.invitations.push(invite);
        });
    },
    deleteInvitation(state, deletedInvitation) {
        state.pendingEmails.splice(state.pendingEmails.indexOf(deletedInvitation), 1);
        state.invitations.forEach(function (invitation) {
            if (invitation.email == deletedInvitation) {
                state.invitations.splice(state.invitations.indexOf(invitation), 1);
            }
        });
    },
    refreshListingType(state, type) {
        state.projects_listing_type = type;
    },
    deleteTeamMembers(state, deletedMember) {
        state.teamMembers.forEach(function (member) {
            if (member.id == deletedMember) {
                state.teamMembers.splice(state.teamMembers.indexOf(member), 1);
                state.exitingEmails.splice(state.exitingEmails.indexOf(member.email), 1);
            }
        });
    },
    recentDatas(state, data) {
        state.clientEmails = data.clientEmails;
        state.clientNames = data.clientNames;
        state.projectCompanies = data.projectCompanies;
    },
    updateRecentData(state, data) {
        switch (data.field) {
            case 'company':
                state.projectCompanies.splice(state.projectCompanies.indexOf(data.item), 1);
                break;
            case 'name':
                state.clientNames.splice(state.clientNames.indexOf(data.item), 1);
                break;
        }
    },
    getActiveSubscription(state, data) {
        state.activeSubscription = data.subscription;
        state.activePlan = data.plan;
        state.ownedTeams = data.ownedTeams;
    },
    getPaymentMethods(state, data) {
        state.paymentMethods = data.paymentMethods.data;
        state.defaultMethod = data.defaultMethod;
    },
    updatePaymentMethods(state, data) {
        if (data.type == 'add') {
            state.paymentMethods.unshift(data.card);
        }
    },
    removePaymentMethod(state, data) {
        let index = state.paymentMethods.map(item => item.id).indexOf(data.id);
        state.paymentMethods.splice(index, 1);
    },

    CHANGE_PLAN(state, data) {
        state.changePlanStatus = data
    },

    UPDATE_USER_PROFILE(state, data) {
        state.user.profiles = data.data;
    },

    UPDATE_USER(state, data) {
        state.user.name = data.data.name;
        state.user.email = data.data.email;
        data.data.current_profile ? state.user.current_profile = data.data.current_profile : '';
        state.user.photo_url = data.data.photo_url;
    },

    UPDATE_NOTIFICATION(state, data) {
        state.user.email_notifications = data.data;
    },

    UPDATE_USER_CURRENT_PROFILE(state, data) {
        state.user.current_profile_id = data.data;
    },

    UPDATE_USER_CURRENT_TEAM(state, data) {
        state.user.current_team_id = data.data;
    }
};

/*
* module actions
*/

const actions = {
    bootstrap({ dispatch, commit }) {
        commit(LOADING_PENDING);
        return User.bootstrap().then(data => {
            commit(LOADING_COMPLETED);
            if (data.status) {
                commit(BOOTSTRAP, data.data);
                dispatch('identifyUser', data.data.user);
            } else {
                toastr['error'](data.errors, 'Error');
            }
        });
    },
    userStorage({ commit }) {
        return User.userStorage().then(data => {
            if (data.status) {
                commit(USER_STORAGE, data.data);
            } else {
                toastr['error'](data.errors, 'Error');
            }
        });
    },
    profile({ commit }, id) {
        return User.profile(id).then(data => {
            if (data.status) {
                commit(PROFILE, data.data);
            } else {
                toastr['error'](data.errors, 'Error');
            }
        });
    },
    updateProfileDescription({ commit }, id) {
        return User.updateProfileDescription(id).then(data => {
            if (data.status) {
                commit(UPDATE_PROFILE, data.data);
            } else {
                toastr['error'](data.errors, 'Error');
            }
        });
    },
    updateHourlyRate({ commit }, id) {
        return User.updateHourlyRate(id).then(data => {
            if (data.status) {
                commit(UPDATE_PROFILE, data.data);
            } else {
                toastr['error'](data.errors, 'Error');
            }
        });
    },
    updateNotificationReview({ commit }, val) {
        return User.updateNotificationReview(val).then(data => {
            if (data.status) {
                commit(UPDATE_REVIEW_PROJECT_NOTIFY, data.data);
            } else {
                toastr['error'](data.errors, 'Error');
            }
        });
    },
    identifyUser({ commit }, user) {
        // // Identify logged in user on Gist
        // convertfox.identify(user.id, {
        //     email: user.email,
        //     name: user.name
        // });
    },
    changeProjectsListingType({ commit }, type) {
        return User.changeProjectsListingType(type).then(data => {
            if (data.status) {
                commit('refreshListingType', type);
            } else {
                toastr['error'](data.errors, 'Error');
            }
        });
    },
    addInvitation({ commit }, members) {
        return commit('addInvitation', members);
    },
    deleteTeamMembers({ commit }, deletedMember) {
        return commit('deleteTeamMembers', deletedMember);
    },
    deleteInvitation({ commit }, invitation) {
        return commit('deleteInvitation', invitation);
    },
    getRecentDatas({ commit }) {
        return User.getRecentData().then(data => {
            if (data.status) {
                commit('recentDatas', data.data);
            } else {
                toastr['error'](data.errors, 'Error');
            }
        });
    },
    updateRecentData({ commit }, data) {
        commit('updateRecentData', data);
    },
    getActiveSubscription({ commit }) {
        commit(LOADING_PENDING);
        return User.getActiveSubscription().then(data => {
            commit(LOADING_COMPLETED);
            if (data.status) {
                commit('getActiveSubscription', data.data);
            } else {
                toastr['error'](data.errors, 'Error');
            }
        });
    },
    getPaymentMethods({ state, commit }, user) {
        commit(LOADING_PENDING);
        state.paymentMethods = [];
        return User.getPaymentMethods(user).then(data => {
            commit(LOADING_COMPLETED);
            if (data.status) {
                commit('getPaymentMethods', data.data);
            } else {
                toastr['error'](data.errors, 'Error');
            }
        });
    },
    updatePaymentMethods({ commit }, data) {
        commit('updatePaymentMethods', data);
    }
};

export default {
    state,
    getters,
    mutations,
    actions
};
