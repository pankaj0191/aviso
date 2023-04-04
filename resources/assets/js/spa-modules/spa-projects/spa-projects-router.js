import Vue from 'vue';
import VueRouter from 'vue-router';
import AuthService from '../../services/auth';
Vue.use(VueRouter);

import FullNavbar from './layout/full-page/FullNavbar.vue';
import ProjectsContainer from './ProjectsContainer.vue';
// import ProjectsList from './ProjectsList.vue';
import Dashboard from './Dashboard.vue';

// Request Project
import RequestName from './request/RequestName.vue';
import RequestProject from './request/Request.vue';
// Request Project

import TimeTracker from './TimeTracker.vue';
import Calender from './Calender.vue';
import Drive from './Drive.vue';
import Clients from './clients/Clients.vue';
import Teams from './teams/Teams.vue';
import Invoice from './invoices/Invoice.vue';
import Invoices from './invoices/Invoices.vue';
import Profile from './profile/Profile.vue';
// import ProjectsCreate from './ProjectsCreate.vue';
import ProjectsFiles from './ProjectsFiles.vue';
// import Proofer from './Proofer.vue';
import Proofer from './Proofer.2.vue';
import ProjectUpdate from './ProjectUpdate';
import ProjectTeamMembers from './ProjectTeamMembers';
import ProjectCreativeBrief from './ProjectCreativeBrief';

// Prooflo V3
import Default from './v3/layouts/default';
import Projects from './v3/Pages/Projects/Show';
import CreateAccount from './v3/Pages/CreateAccount/CreateAccount';
import CreateProject from './v3/Pages/CreateProject/Show';
import ProjectTitle from './v3/Pages/CreateProject/components/ProjectTitle';
import ProjectType from './v3/Pages/CreateProject/components/ProjectCategory/ProjectType';
import ExistProject from './v3/Pages/CreateProject/components/ExistProject';
import ProjectBudget from './v3/Pages/CreateProject/components/Budget/Budget';
import ProjectDescrip from './v3/Pages/CreateProject/components/Description/Description';
import ProjectUpload from './v3/Pages/CreateProject/components/UploadProofs';
import ProjectShare from './v3/Pages/CreateProject/components/Share';
import ProjectSummary from './v3/Pages/CreateProject/components/Summary/Summary';
import ProjectInvitation from './v3/Pages/ProjectInvitation/ProjectInvitation';
import Categories from './v3/Pages/Categories/Categories';
import MyTasks from './v3/Pages/MyTasks/MyTasks';

// Setting Page
import Settings from './v3/Pages/Settings/Show';
import UpdateProfileInformation from "./v3/Pages/Settings/components/UpdateProfileInformation";
import ChangePassword from "./v3/Pages/Settings/components/ChangePassword";
import BillingSettings from "./v3/Pages/Settings/components/Subscription/Subscriptions";
import NotificationSettings from "./v3/Pages/Settings/components/Notification";
import PaymentMethodSettings from "./v3/Pages/Settings/components/PaymentMethod/PaymentMethod.vue";

import PageNotFound from './v3/Pages/Pages/NotFound';
import PageNotAuthorized from './v3/Pages/Pages/NotAuthorized';


function authenticate(to, from, next, guards) {
    axios.post(location.origin + '/api/auth/verifyProjectUser', { 'project_id': to.params.project_id })
        .then(({ data }) => {
            let passes = [];
            guards.forEach(guard => {
                passes.push(data.hasOwnProperty(guard) && data[guard]);
            });

            if (passes.filter(pass => pass === false).length > 0) {
                return next('/dashboard');
            }

            next();
        })
        .catch(errors => {
            next('/dashboard');
            console.log(errors);
        });
}

const routes = [
    //GENERAL

    // Prooflo V3
    {
        path: '/dashboard', component: Default, name: 'dashboard',
        children: [
            {
                path: '',
                component: Dashboard,
                name: 'dashboard',
                props: true,
                meta: { requiresAuth: true, title: 'Dashboard' }
            },
            {
                path: 'settings',
                component: Settings,
                name: 'settings',
                props: true,
                meta: { requiresAuth: true, title: 'Settings' },
                children: [
                    {
                        path: 'update-profile',
                        name: 'setting-profile',
                        component: UpdateProfileInformation,
                        props: true,
                        meta: {
                            title: 'Update Profile Information'
                        },
                    },
                    {
                        path: 'change-password',
                        name: 'setting-change-password',
                        component: ChangePassword,
                        props: true,
                        meta: {
                            title: 'Change Password'
                        },
                    },
                    {
                        path: 'notification',
                        name: 'setting-notification',
                        component: NotificationSettings,
                        props: true,
                        meta: {
                            title: 'Notification'
                        },
                    },
                    {
                        path: 'billing',
                        name: 'setting-billing',
                        component: BillingSettings,
                        props: true,
                        meta: {
                            title: 'Billing'
                        },
                    },
                    {
                        path: 'payment-methods',
                        name: 'setting-payment-methods',
                        component: PaymentMethodSettings,
                        props: true,
                        meta: {
                            title: 'Payment Methods'
                        },
                    },
                ]
            },
            { path: 'projects', component: Projects, name: 'projects', props: true, meta: { title: 'Prooflo App' } },
            {
                path: 'create-account',
                name: 'create_account',
                component: CreateAccount,
                props: true,
                meta: {
                    title: 'Create new account'
                }
            },
            {
                path: 'invitation/:token',
                name: 'project_invitation',
                component: ProjectInvitation,
                props: true,
                meta: {
                    title: 'Project Inviation'
                }
            },
            {
                path: 'create/:role',
                name: 'create-project',
                component: CreateProject,
                props: true,
                meta: {
                    title: 'Create project'
                },
                children: [
                    {
                        path: 'project-type',
                        name: 'project-type',
                        component: ProjectType,
                        props: true,
                        meta: {
                            title: 'Project Types'
                        },
                    },
                    {
                        path: ':type/new/title',
                        name: 'project-title',
                        component: ProjectTitle,
                        props: true,
                        meta: {
                            title: 'Create project'
                        },
                    },
                    {
                        path: ':type/:project_id',
                        name: 'exist-project',
                        component: ExistProject,
                        props: true,
                        meta: {
                            title: 'Create project'
                        },
                        children: [
                            {
                                path: 'title',
                                name: 'exist-project-title',
                                component: ProjectTitle,
                                props: true,
                                meta: {
                                    title: 'Create project'
                                },
                            },
                            {
                                path: 'budget',
                                name: 'project-budget',
                                component: ProjectBudget,
                                props: true,
                                meta: {
                                    title: 'Create project'
                                },
                            },
                            {
                                path: 'descrip',
                                name: 'project-descrip',
                                component: ProjectDescrip,
                                props: true,
                                meta: {
                                    title: 'Create project'
                                },
                            },
                            {
                                path: 'share',
                                name: 'project-share',
                                component: ProjectShare,
                                props: true,
                                meta: {
                                    title: 'Create project'
                                },
                            },
                            {
                                path: 'upload',
                                name: 'project-upload',
                                component: ProjectUpload,
                                props: true,
                                meta: {
                                    title: 'Create project'
                                },
                            },
                            {
                                path: 'summary',
                                name: 'project-summary',
                                component: ProjectSummary,
                                props: true,
                                meta: {
                                    title: 'Create project'
                                },
                            },
                        ]
                    }
                ]
            },
            {
                path: 'categories',
                component: Categories,
                name: 'categories',
                props: true,
                meta: { requiresAuth: true, title: 'Categories' }
            },
            {
                path: 'my-tasks',
                component: MyTasks,
                name: 'my-tasks',
                props: true,
                meta: { requiresAuth: true, title: 'Tasks' }
            },
            {
                path: 'time-trackers',
                component: TimeTracker,
                name: 'time-trackers',
                props: true,
                meta: {
                    requiresAuth: true, title: 'Time Tracker'
                }
            },
            {
                path: 'calender',
                component: Calender,
                name: 'calender',
                props: true,
                meta: { requiresAuth: true, title: 'Calender' }
            },
            {
                path: 'drive',
                component: Drive,
                name: 'drive',
                props: true,
                meta: { requiresAuth: true, title: 'Drive' }
            },
            {
                path: 'clients',
                component: Clients,
                name: 'clients',
                props: true,
                meta: { requiresAuth: true, title: 'Clients' }
            },
            {
                path: 'teams',
                component: Teams,
                name: 'teams',
                props: true,
                meta: { requiresAuth: true, title: 'Teams' }
            },
            {
                path: '/projects/files/:project_id/:revision_id',
                component: ProjectsFiles,
                name: 'upload_files_with_revision',
                props: true,
                beforeEnter: (to, from, next) => authenticate(to, from, next, ['isProjectUser', 'isFreelancer']),
                meta: { requiresAuth: true, title: 'Prooflo App' },
            },
            {
                path: '/projects/creative-brief/:project_id/:revision_id',
                component: ProjectCreativeBrief,
                name: 'project_creative_brief',
                props: true,
                beforeEnter: (to, from, next) => authenticate(to, from, next, ['isProjectUser']),
            },
            {
                path: '/projects/team-members/:project_id',
                component: ProjectTeamMembers,
                name: 'add_team_members',
                props: true,
                beforeEnter: (to, from, next) => authenticate(to, from, next, ['isProjectUser', 'isFreelancer']),
            },
            {
                path: '/projects/files/:project_id',
                component: ProjectsFiles,
                name: 'upload_files',
                props: true,
                beforeEnter: (to, from, next) => authenticate(to, from, next, ['isProjectUser', 'isFreelancer']),
            },
            {
                path: '/projects/update/:project_id',
                component: ProjectUpdate,
                name: 'update_project',
                props: true,
                beforeEnter: (to, from, next) => authenticate(to, from, next, ['isProjectUser', 'isFreelancer']),
            }
        ]
    },

    //PROOFER FREELANCER
    {
        path: '/proofer/:project_id/:revision_id',
        component: Proofer,
        name: 'proofer',
        props: true,
        meta: { requiresAuth: true, title: 'Proofs' }
    },

    {
        path: '/proofer/:project_id/:revision_id/:proof_id',
        component: Proofer,
        name: 'proofer_proof',
        props: true,
        meta: { requiresAuth: true, title: 'Proof' }
    },

    {
        path: '/proofer/:project_id/:revision_id/:proof_id/:issue_id',
        component: Proofer,
        name: 'proofer_proof_issue',
        props: true,
        meta: { requiresAuth: true, title: 'Proofs' }
    },

    //PROOFER CLIENT
    {
        path: '/invoices/:id',
        component: Invoice,
        name: 'invoice',
        props: true,
        meta: { requiresAuth: true, title: 'Invoice' }
    },
    {
        path: '/invoices',
        component: Invoices,
        name: 'invoices',
        props: true,
        meta: { requiresAuth: true, title: 'Invoices' }
    },

    {
        path: '/', component: FullNavbar,
        children: [
            {
                path: '/profile/:username',
                component: Profile,
                name: 'profile',
                props: true,
                meta: { requiresAuth: true, title: 'Profile' }
            },
            {
                path: '/request/:project_id/:revision_id',
                component: RequestProject,
                name: 'request-project',
                props: true,
                meta: { requiresAuth: true, title: 'Request' }
            },
            {
                path: '/request',
                component: RequestName,
                name: 'create-request',
                props: true,
                meta: { requiresAuth: true, title: 'Create Request' }
            },
        ]
    },
    { path: "/page/not-authorized", component: PageNotAuthorized, name: 'not-auth' },
    { path: "/dashboard/*", component: PageNotFound },
    { path: "/request/*", component: PageNotFound },
    { path: "/profile/*", component: PageNotFound },
    { path: "/proofer/*", component: PageNotFound }
];


const router = new VueRouter({
    routes,
    mode: 'history',
    linkActiveClass: 'active'
});

// This callback runs before every route change, including on page load.
router.beforeEach((to, from, next) => {
    // Get the page title from the route meta data that we have defined
  // See further down below for how we setup this data
  const title = to.meta.title
  // If the route has a title, set it as the page title of the document/page
    if (title) {
      document.title = title
    }
    // Continue resolving the route
    next()
});

export default router;