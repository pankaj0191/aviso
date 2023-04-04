<?php

use App\Project;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register the API routes for your application as
| the routes are automatically authenticated using the API guard and
| loaded automatically by this application's RouteServiceProvider.
|
 */

Route::post('slack/connect', 'SlackController@connect');
Route::group(['middleware' => 'auth:api', 'prefix' => 'auth'], function () {
    Route::post('verifyProjectUser', function () {
        $project = Project::findOrFail(request('project_id'));

        return response()->json([
            'isProjectUser' => $project->users->contains(request()->user()),
            'isFreelancer' => request()->user()->isFreelancerOf($project),
            'isClient' => request()->user()->isClientOf($project),
        ], 200);
    });

    Route::post('login_by_user_id', 'AuthController@authenticateByUserId');
    Route::get('getCurrentRole/{project_id}', 'AuthController@getCurrentRole');
    Route::get('check', 'AuthController@check');
});


Route::group(['middleware' => 'auth:api', 'prefix' => 'bootstrap'], function () {
    Route::get('/', 'BootstrapController@bootstrap');
    Route::get('/user-storage', 'BootstrapController@userStorage');
    Route::get('/profile/{username}', 'BootstrapController@profile');
    Route::put('/change-projects-listing-type', 'BootstrapController@changeProjectsListingType');
    Route::get('/get-recent-datas', 'BootstrapController@getRecentDatas');
    Route::put('/disable-autocomplete-data', 'BootstrapController@disableAutocompleteData');
    Route::get('/active-subscription', 'BootstrapController@getActiveSubscription');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'payments'], function () {
    Route::get('/get-payment-methods/{customer}', 'PaymentMethodController@list');
});

Route::group(['middleware' => 'auth:api','prefix' => 'files'], function () {
    Route::post('upload', 'FileController@upload');
    Route::delete('delete/{id}', 'FileController@deleteFile');
    Route::delete('delete', 'FileController@deleteFiles');
    Route::delete('deleteFinalFile/{projectId}/{id}', 'FileController@deleteFinalFile');
    Route::post('deleteFinalFiles', 'FileController@deleteFinalFiles');
    Route::get('get_file/{file_id}', 'FileController@getById');
    Route::post('videocaptureupload', 'FileController@videoCaptureUpload');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'projects'], function () {
    Route::get('bootstrap', 'BootstrapController@bootstrap');
    Route::resource('/', 'ProjectController');
    Route::resource('/project-timers', 'ProjectTimerController');
    Route::post('projects/send', 'ProjectController@send');
    Route::get('categories', 'ProjectController@getCategories');
    Route::post('active/category', 'ProjectController@activeCategory');
    Route::post('active/project-type', 'ProjectController@activeProjectType');
    Route::post('project-type', 'ProjectController@createProjectType');
    Route::post('project-type/image', 'ProjectController@createProjectTypeImage');
    Route::delete('project-type/{id}', 'ProjectController@destroyProjectType');
    Route::post('project-type/{id}', 'ProjectController@editProjectType');
    Route::post('project-type/files/{id}', 'ProjectController@uploadFileProjectType');
    Route::delete('project-type/files/{id}', 'ProjectController@deleteFileProjectType');
    Route::delete('delete_project/{project_id}', 'ProjectController@deleteProject');
    Route::post('bulk/delete', 'ProjectController@bulkDeleteProject');
    Route::get('details/{project_id}', 'ProjectController@getDetails');
    Route::put('update/{project_id}', 'ProjectController@updateProject');
    Route::put('update/budget/{project_id}', 'ProjectController@updateProjectBudget');
    Route::put('update/description/{project_id}', 'ProjectController@updateProjectDescription');
    Route::put('update/summary/{project_id}', 'ProjectController@submitProjectSummary');
    Route::put('invitation-email/{project_id}', 'ProjectController@invitationEmail');
    Route::delete('{project}/collaborators/{invitation}/invitation', 'ProjectController@deleteInvitation');
    Route::get('invitation/{token}', 'ProjectController@invitationToken');
    Route::post('invitation/accept/{token}', 'ProjectController@invitationAccept');
    Route::post('invitation/decline/{token}', 'ProjectController@invitationDecline');
    Route::get('clients/fetch', 'ProjectController@fetchClients');
    Route::get('freelancers/fetch', 'ProjectController@fetchClients');
    Route::put('update-rate/{project_id}', 'ProjectController@updateRate');
    Route::put('change_status/{project_id}/{status}', 'ProjectController@changeStatus');
    Route::post('add-team-member', 'ProjectController@addTeamMember');
    Route::delete('delete-team-member/{project_id}/{member_id}', 'ProjectController@deleteTeamMember');

    Route::post('create', 'ProjectController@store');
    /*         Route::post('sendProofs', 'ProjectController@sendProofs'); */
    Route::get('{project_id}/revisions', 'ProjectController@getRevisions');
    Route::get('{revision_id}/partial_proofs', 'RevisionController@getPartialProofs');
    Route::get('load/{project_id}/{revison_id}', 'ProjectController@loadInitialRevision');
    Route::get('send_project/{project_id}/{user_type}', 'ProjectController@sendProject');
    Route::post('approve_project', 'ProjectController@approveProject');
    Route::get('get_user_rol/{project_id}', 'ProjectController@getCurrentUserRol');
    Route::get('submit_corrections/{project_id}', 'ProjectController@submitCorrections');
    Route::get('files/{project_id}', 'FileController@getFiles');
    Route::get('getFinalFiles/{project_id}', 'ProjectController@getFinalFiles');
    Route::get('download-zip/{project}', 'ProjectController@downloadZip');
    Route::post('sendFinalFiles', 'ProjectController@sendFinalFiles');
    Route::post('inviteCollaborators', 'ProjectController@inviteCollaborators');
    Route::post('save-creative-brief', 'ProjectController@saveCreativeBrief');
    Route::get('creative-brief/{project_id}', 'ProjectController@getCreativeBrief');
    Route::post('poke', 'ProjectController@poke');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'proof'], function () {
    Route::post('save', 'ProofController@saveProofState');
    Route::post('create_issue', 'ProofController@createIssue');
    Route::get('issues', 'ProofController@getIssues');
    Route::post('issue/assisng', 'ProofController@assignIssue');
    Route::post('issue/due-date', 'ProofController@dueDate');
    Route::post('issue/like', 'ProofController@addLiked');
    Route::post('issue/priority', 'ProofController@issuePriority');
    Route::delete('delete_issue/{issue_id}', 'ProofController@deleteIssue');
    Route::post('add_comment', 'ProofController@addComment');
    Route::delete('delete_comment/{comment_id}', 'ProofController@deleteComment');
    Route::get('change_proof_status/{proof_id}/{status}', 'ProofController@changeProofStatus');
    Route::get('change_issue_status/{issue_id}/{status}/{project_typefre}', 'ProofController@changeIssueStatus');
    Route::delete('delete_proof/{proof_id}', 'ProofController@deleteProof');
    Route::delete('delete_issue_unread_comments/{issue_id}', 'ProofController@deleteIssueUnreadComments');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'revisions'], function () {
    Route::post('create', 'RevisionController@create');
    Route::delete('delete/{project_id}', 'RevisionController@remove');
    Route::get('get_by_project/{project_id}', 'RevisionController@getRevisionsByProject');
    Route::get('load_revision_by_id/{revision_id}', 'RevisionController@loadRevisionById');
    /* Route::get('change_revision_status/{project_id}/{version}/{status}', 'RevisionController@changeRevisionStatus'); */
});

//Extension API routes
Route::group(['prefix' => 'v1'], function () {
    Route::post('login', 'API\V1\AuthController@login');
    Route::get('extension-data/{user_id}/{project_id}', 'API\V1\APIController@getExtensionData')
        ->middleware('auth:api');

    Route::middleware(['authorized'])->group(function () {
        Route::get('projects', 'API\V1\APIController@projects');
        Route::post('upload', 'API\V1\APIController@uploadProofs');
        Route::post('uploadVideo', 'API\V1\APIController@uploadVideo');
    });
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'teams'], function () {
    Route::post('invite-members', 'TeamController@inviteMembers');
    Route::get('invitations/{team_id}', 'TeamController@invitations');
});

//Admin
Route::group(['middleware' => 'auth:api, dev', 'prefix' => 'admin'], function () {
    //Users
    Route::group(['before' => 'auth', 'prefix' => 'users'], function () {
        Route::get('all', 'Admin\UserController@list');
        Route::get('active-subscription/{user}', 'Admin\UserController@getActiveSubscription');
        Route::delete('delete/{id}', 'Admin\UserController@delete');
        Route::post('search', 'Admin\UserController@search');
    });
});

//Subscriptions & payments
Route::group(['middleware' => 'auth:api', 'prefix' => 'settings'], function () {
    Route::post('/subscribe-with-new-card', 'SubscriptionController@subscribeWithNewCard');
    Route::post('/subscribe-with-exiting-card', 'SubscriptionController@subscribeWithExitingCard');
    Route::post('/swap-subscription-with-new-card', 'SubscriptionController@swapWithNewCard');
    Route::post('/swap-subscription-with-exiting-card', 'SubscriptionController@swapWithExitingCard');
    Route::post('/resume-subscription', 'SubscriptionController@resumeSubscription');
    Route::post('/cancel-subscription', 'SubscriptionController@cancelSubscription');
    Route::post('/add-payment-method', 'PaymentMethodController@create');
    Route::post('/remove-payment-method', 'PaymentMethodController@delete');
    Route::post('/set-as-default-payment', 'PaymentMethodController@setAsDefault');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'request'], function () {
    Route::get('/', 'ClientRequestController@freelancers');
    Route::get('/workers', 'ClientRequestController@workers');
    Route::post('/instruction', 'ClientRequestController@instruction');
    Route::post('/dimension', 'ClientRequestController@dimension');
    Route::post('/name', 'ClientRequestController@updateName');
    Route::post('/description', 'ClientRequestController@description');
    Route::post('/summary', 'ClientRequestController@summary');
    Route::post('/draft', 'ClientRequestController@draft');
    Route::post('/create', 'ClientRequestController@store');
    Route::get('/download-zip/{file}', 'ClientRequestController@downloadZipFile');
    Route::get('/fetch', 'ClientRequestController@create');
    Route::get('/files/{project_id}', 'ClientRequestController@getFiles');
    Route::get('/unsplash', 'UnsplashController@searchUnsplash');
    Route::get('/unsplash-download/{id}', 'UnsplashController@unsplashDownload');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'calender'], function () {
    Route::post('/{project_id}', 'CalenderController@update');
    Route::post('/remove/{project_id}', 'CalenderController@remove');
    Route::get('/google-calender', 'CalenderController@index');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'drive'], function () {
    Route::get('/', 'DriveController@index');
    Route::post('/download', 'DriveController@download');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'my-users'], function () {
    Route::get('/', 'MyUsersController@index');
    Route::get('/teams', 'MyUsersController@teams');
    Route::post('/update', 'MyUsersController@update');
});
Route::group(['middleware' => 'auth:api', 'prefix' => 'invoices'], function () {
    Route::get('/', 'InvoiceController@index');
    Route::get('/{id}', 'InvoiceController@show');
    Route::post('/send/{id}', 'InvoiceController@sendMail');
    Route::post('/send', 'InvoiceController@sendMailInvoices');
    Route::post('/change-logo', 'InvoiceController@changeLogo');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'profile'], function () {
    Route::post('/', 'ProfileController@store');
    Route::post('/hourly-rate', 'ProfileController@update');
    Route::post('/update-notify', 'ProfileController@updateNotify');
    Route::post('/create-profile', 'ProfileController@createProfile');
    Route::put('/update/{id}', 'ProfileController@updateProfile');
    Route::post('/update/user', 'ProfileController@updateUserInfo');
    Route::post('/update/password', 'ProfileController@updatePassword');
    Route::post('/create-profile', 'ProfileController@createProfile');
    Route::post('/delete-profile/{id}', 'ProfileController@destroy');
    Route::post('/switch-profile', 'ProfileController@switchProfile');
    Route::post('/switch-team', 'ProfileController@switchTeam');
    Route::delete('/delete/photo', 'ProfileController@deletePhoto');
    Route::post('/update/notifcation', 'ProfileController@updateNotification');
});

Route::get('/callback/calender', [
    'as' => 'oauth',
    'uses' => 'CalenderController@store'
]);
