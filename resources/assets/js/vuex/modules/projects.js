// system.js
// main module
//----------------------------------

import project from "../../models/project";
import Project from "../../models/project";
import Vue from 'vue'
import { PROJECTS_LOAD } from "../mutation-types.js";

/*
 * state
 *
 */

const state = {
    projects: {},
    filterProjects: [],
    in_progress: {},
    on_revision: {},
    on_draft: {},
    on_hold: {},
    approved: {},
    completed: {},
    member_projects: {},
    current_member: '',
    current_proofs_state: '',
    show_project_by_status: 1,
    current_project_listing: 'progress',
    is_freelancer: {},
    is_agency: {},
    is_collaborator: {},
    loadingStatus: false,
};

/*
* module getters
*/

const getters = {
    projects: state => state.projects,
    projectsFilter(state) {
        if (state.show_project_by_status == 0) {
            return state.filterProjects = state.projects;
        } else if (state.show_project_by_status == 1) {
            return state.filterProjects = state.in_progress;
        } else if (state.show_project_by_status == 2) {
            return state.filterProjects = state.on_revision;
        } else if (state.show_project_by_status == 3) {
            return state.filterProjects = state.approved;
        } else if (state.show_project_by_status == 4) {
            return state.filterProjects = state.completed;
        } else if (state.show_project_by_status == 5) {
            return state.filterProjects = state.on_draft;
        } else if (state.show_project_by_status == 6) {
            return state.filterProjects = state.on_hold;
        } else {
            return state.filterProjects = state.member_projects;
        }
    },
    current_proofs_state: state => state.current_proofs_state,
    in_progress: state => state.in_progress,
    on_revision: state => state.on_revision,
    approved: state => state.approved,
    completed: state => state.completed,
    on_draft: state => state.on_draft,
    on_hold: state => state.on_hold,
    member_projects: state => state.member_projects,
    current_member: state => state.current_member,
    listing: state => state.current_project_listing,
    is_freelancer: state => state.is_freelancer,
    is_agency: state => state.is_agency,
    is_collaborator: state => state.is_collaborator,
    loadingStatus: state => state.loadingStatus
};

/*
* module mutations
*/

const mutations = {
    /**
     * Always called on initial page load, which occurs in DashboardLayout
     * broadcast data-ready event
     */
    [PROJECTS_LOAD](state, data) {
        if (data) {
            state.projects = data;
            state.in_progress = data.filter(project => ((project.status == 'progress') || (project.type == 'website' && project.status == 'revision' && project.total_issues > project.review_issues)));
            state.on_revision = data.filter(project => (((project.status == 'revision' && project.type != 'website') || (project.type == 'website' && project.status == 'revision' && project.total_issues == project.review_issues)) || (project.role.toLowerCase() != 'freelancer' && project.role.toLowerCase() != 'agency') && (project.created_role == 'freelancer' || project.created_role == 'agency') && (project.status == 'draft' && (project.created_role == 'freelancer' || project.created_role == 'agency') || (project.type == 'website' && project.status == 'revision' && project.total_issues == project.review_issues))));
            state.on_draft = data.filter(project => project.status == 'draft' && (project.role.toLowerCase() == 'agency' || project.role.toLowerCase() == 'freelancer' || project.created_role == 'client'));
            state.on_hold = data.filter(project => project.status == 'hold');
            state.approved = data.filter(project => project.status == 'approved');
            state.completed = data.filter(project => project.status == 'completed');
            state.is_freelancer = data.filter(project => project.role.toLowerCase() == 'freelancer');
            state.is_agency = data.filter(project => project.role.toLowerCase() == 'agency');
            state.is_collaborator = data.filter(project => project.role.toLowerCase() == 'collaborator');
        }
    },

    SAVE_CURRENT_PROOF_DATA(state, data) {
        state.current_proofs_state = data;
    },

    RESET_CURRENT_PROOF_DATA(state) {
        state.current_proofs_state = [];
    },

    SAVE_CURRENT_PROJECT_NAME(state, data) {
        state.current_project_name = data;
    },

    SAVE_PROGRESS_PROJECT(state, data) {
        state.in_progress_projects = data;
    },

    SAVE_REVISION_PROJECTS(state, data) {
        state.on_revison_projects = data;
    },

    SAVE_APPROVED_PROJECTS(state, data) {
        state.approved_projects = data;
    },

    SAVE_COMPLETED_PROJECTS(state, data) {
        state.completed_projects = data;
    },

    SAVE_DRAFT_PROJECTS(state, data) {
        state.on_draft_projects = data;
    },
    set_show_project_by_status(state, data) {
        state.show_project_by_status = data;
    },

    set_current_project_listing(state, data) {
        state.current_project_listing = data;
    },
    showMemberProjects(state, member) {
        state.current_member = member.name;
        state.member_projects = state.projects.filter(project => project.users.indexOf(member.id) >= 0);
    },

    loadingStatus(state, newLoadingStatus) {
        state.loadingStatus = newLoadingStatus;
    },

    DELETE_PROJECT(state, project_id) {
        var index = state.projects.map(project => project.id).indexOf(project_id);
        state.projects.splice(index, 1);
        this.commit(PROJECTS_LOAD, state.projects);
    },

    BULK_DELETE_PROJECT(state, selected) {
        console.log(selected, 'sss')
        selected.forEach(value => {
            console.log(selected, value, 'a')
            var index = state.projects.map(project => project.id).indexOf(value);
            state.projects.splice(index, 1);
            this.commit(PROJECTS_LOAD, state.projects);
        })
    },

    UPDATE_PROJECT_STATUS(state, newProject) {
        const index = state.projects.map(project => project.id).indexOf(newProject.id);
        state.projects.splice(index, 1, newProject);
        this.commit(PROJECTS_LOAD, state.projects);
    }

};

/*
* module actions
*/

const actions = {

    loadProjects({ dispatch, commit }) {
        commit('loadingStatus', true);

        return Project.list().then(projects => {
            commit(PROJECTS_LOAD, Object.values(projects));
            commit('loadingStatus', false);
        });
    },
    loadMemberProjects({ commit }, member) {
        commit('showMemberProjects', member);
    }
};

export default {
    state,
    getters,
    mutations,
    actions
};
