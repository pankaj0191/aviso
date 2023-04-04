<?php

use App\Project;
use App\Revision;
use App\Spark;
use App\CategoryFile;
use Illuminate\Support\Facades\URL;
use App\Events\NotificationRealTime;
use App\Notifications\SlackNotification;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */

// Route::get('download', 'ProjectController@fetchClients');


Route::get('/', 'WelcomeController@show');

Route::get('/home', function () {
//    dd( App\Traits\ProvidesScriptVariables::scriptVariables());
    return redirect('/dashboard');
});

Route::get('/dev', function () {
    echo 2 * pow(1024, 3);
});
Route::get('/slack', 'SlackController@oauth');
Route::get('/slack-test', 'SlackController@test');


Route::group(['middleware' => ['auth']], function () {
    Route::get('impersonator-check', 'UserController@impersonateCheck');

    // Prooflo v3
    Route::get('/projects/{path?}', function($path = null) {
        return $path ? redirect("/dashboard/" . $path ) : redirect("/dashboard" );
    })->where('path', '^(?!api).*$');

    Route::get('/dashboard/{path?}', function () {
        return view('spa-modules.spa-projects.spa-projects');
    })->where('path', '^(?!api).*$');

    Route::get('/request/{path?}', function () {
        return view('spa-modules.spa-projects.spa-projects');
    })->where('path', '^(?!api).*$');

    Route::get('/profile/{path?}', function () {
        return view('spa-modules.spa-projects.spa-projects');
    })->where('path', '^(?!api).*$');

    Route::get('/invoices/{path?}', function () {
        return view('spa-modules.spa-projects.spa-projects');
    })->where('path', '^(?!api).*$');

    Route::middleware(['verifyProjectUser', 'projectPublished'])->get('/proofer/{project}/{revision}', function (Project $project, Revision $revision) {
        return view('spa-modules.spa-projects.spa-projects');
    });

    Route::get('/proofer/{path?}', function () {
        return view('spa-modules.spa-projects.spa-projects');
    })->where('path', '^(?!api).*$');

    Route::get('/loadProofer/{project_hash}/{revision_id}', 'ProjectController@loadProofer');
    Route::get('/loadProofer/{project_hash}/{revision_id}/{proof_id}', 'ProjectController@loadProofer3');
    Route::get('/loadProofer/{project_hash}/{revision_id}/{proof_id}/{issue_id}', 'ProjectController@loadProofer2');
    /* Route::get('/load_editor_freelancer/{project_id}/{revision_id}', 'ProjectController@loadEditorFreelancer'); */
});

/* Route::get('/load_editor_client/{project_hash}/{revision_id}', 'ProjectController@loadEditorClient');
Route::get('/load_editor_freelancer/{project_id}/{revision_id}', 'ProjectController@loadEditorFreelancer'); */

/* Route::get('/proofer_guest/{path?}', function () {
    return view('spa-modules.spa-projects.spa-projects');
})->where('path', '^(?!api).*$');

Route::get('/proofer_freelancer/{path?}', function () {
    return view('spa-modules.spa-projects.spa-projects');
})->where('path', '^(?!api).*$');
 */

//Email notifications settings
Route::put('/settings/email-notifications', 'EmailNotificationSettingsController@update');

//Custom Notifications
Route::get('/notifications/recent', 'NotificationController@recent');
Route::put('/notifications/read-one', 'NotificationController@markAsReadOne');

Route::get('/notifications/fetch', 'NotificationController@fetch');

//CUSTOM AUTH ROUTES

// Overwrite Spark’s Register function to add User Verification…
Route::get('/register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('/register', 'Auth\RegisterController@register');

// Overwrite Spark’s Register function to add User Verification…
Route::post('/resend', 'Auth\RegisterController@resend');

// Overwrite Spark’s login function to add User Verification…
// Authentication...
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

// Overwrite: Password Reset...
// Overwrite Spark’s Password reset function
Route::get('/password/reset/{token?}', 'Auth\PasswordController@showResetForm')->name('password.reset');
Route::post('/password/email', 'Auth\PasswordController@sendResetLinkEmail');
Route::post('/password/reset', 'Auth\PasswordController@reset');

// Email Verification...
// Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
// Route::get('email/verify/{id}', 'Auth\VerificationController@verify')->name('verification.verify');
// Route::get('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');

// To stop un-verified users getting a reset email…
Route::post('/password/email', 'Auth\PasswordController@sendResetLinkEmail');

//Verify user profile
Route::get('verify-user/{id}/{code}', 'UserController@verifyUser');

//Overwrite Spark’s Delete Team Member function
 $pluralTeamString = str_plural(\App\Spark::teamsPrefix());
 Route::delete('/settings/' . $pluralTeamString . '/{team}/members/{team_member}', 'TeamController@deleteMember');
// Teams...
if (Spark::usesTeams()) {

    // Missing Team Notice...
    Route::get('/'.Spark::teamsPrefix().'/missing', 'MissingTeamController@show');

    // General Settings...
     Route::get('/settings/'.Spark::teamsPrefix().'/roles', 'Settings\Teams\TeamMemberRoleController@all');
     Route::get('/settings/'.Spark::teamsPrefix().'/current', 'TeamController@current');
     Route::get('/settings/'.Spark::teamsPrefix().'/json/{team_id}', 'TeamController@show');

     Route::get('/settings/'.Spark::teamsPrefix().'/{team}', 'Settings\Teams\DashboardController@show')->name('settings.team');
     Route::post('/settings/'.Spark::teamsPrefix().'/{team}/photo', 'Settings\Teams\TeamPhotoController@update');
     Route::put('/settings/'.Spark::teamsPrefix().'/{team}/name', 'Settings\Teams\TeamNameController@update');
     Route::get('/settings/'.Spark::teamsPrefix().'/{team}/invitations', 'Settings\Teams\MailedInvitationController@all');
     Route::post('/settings/'.Spark::teamsPrefix().'/{team}/invitations', 'Settings\Teams\MailedInvitationController@store');
     Route::put('/settings/'.Spark::teamsPrefix().'/{team}/members/{team_member}', 'Settings\Teams\TeamMemberController@update');
     Route::delete('/settings/'.Spark::teamsPrefix().'/{team}/members/{team_member}', 'Settings\Teams\TeamMemberController@destroy');
     Route::delete('/settings/'.Spark::teamsPrefix().'/{team}', 'Settings\Teams\TeamController@destroy');
     Route::get('/settings/'.Spark::teamsPrefix().'/{team}/switch', 'TeamController@switchCurrentTeam');

     Route::get('/settings/'.Spark::teamsPrefix(), 'TeamController@all');
     Route::get(Spark::teamsPrefix(), 'TeamController@all');
     Route::post('/settings/'.Spark::teamsPrefix(), 'Settings\Teams\TeamController@store');

     Route::get('/settings/invitations/pending', 'Settings\Teams\PendingInvitationController@all');
     Route::post('/settings/invitations/{invitation}/accept', 'Settings\Teams\PendingInvitationController@accept');
     Route::post('/settings/invitations/{invitation}/reject', 'Settings\Teams\PendingInvitationController@reject');
     Route::delete('/settings/invitations/{invitation}', 'Settings\Teams\MailedInvitationController@destroy');

    // Billing

    // Subscription Settings...
     Route::post('/settings/'.Spark::teamsPrefix().'/{team}/subscription', 'Settings\Teams\Subscription\PlanController@store');
     Route::put('/settings/'.Spark::teamsPrefix().'/{team}/subscription', 'Settings\Teams\Subscription\PlanController@update');
     Route::delete('/settings/'.Spark::teamsPrefix().'/{team}/subscription', 'Settings\Teams\Subscription\PlanController@destroy');

    // VAT ID Settings...
     Route::put('/settings/'.Spark::teamsPrefix().'/{team}/payment-method/vat-id', 'Settings\Teams\PaymentMethod\VatIdController@update');

    // Credit Card Settings...
     Route::put('/settings/'.Spark::teamsPrefix().'/{team}/payment-method', 'Settings\Teams\PaymentMethod\PaymentMethodController@update');

    // Redeem Coupon...
     Route::post('/settings/'.Spark::teamsPrefix().'/{team}/payment-method/coupon', 'Settings\Teams\PaymentMethod\RedeemCouponController@redeem');

    // Billing History...
     Route::put(
        '/settings/'.Spark::teamsPrefix().'/{team}/extra-billing-information',
        'Settings\Teams\Billing\BillingInformationController@update'
    );

    // Coupons...
     Route::get('/settings/'.Spark::teamsPrefix().'/coupon/{id}', 'TeamCouponController@current');

    // Invoices...
     Route::get('/settings/'.Spark::teamsPrefix().'/{team}/invoices', 'Settings\Teams\Billing\InvoiceController@all');
     Route::get('/settings/'.Spark::teamsPrefix().'/{team}/invoice/{id}', 'Settings\Teams\Billing\InvoiceController@download');
}

// Invoices...
Route::get('/settings/invoices', 'Settings\Billing\InvoiceController@all');
Route::get('/settings/invoice/{id}', 'Settings\Billing\InvoiceController@download');


//Plans management
Route::get('plans/list', 'PlanController@list');
Route::group([/*'middleware' => 'dev', */ 'prefix' => 'plans'], function () {
    Route::get('{planId}', 'PlanController@show');
    Route::post('store', 'PlanController@store');
    Route::put('update/{planId}', 'PlanController@update');
    Route::delete('delete/{planId}', 'PlanController@delete');
});

// Settings Dashboard...
Route::get('/settings/{tab}', 'DashboardController@showByTab');
Route::get('/google-calender', 'CalenderController@index');


//Stripe Webhook Handler
Route::post('/stripe/webhook', 'StripeWebhookController@handleWebhook')->middleware('verifyStripeSignature');

// Update profile details
Route::put('/settings/profile/details', 'ProfileDetailsController@update');
