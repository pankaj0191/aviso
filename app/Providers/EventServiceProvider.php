<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // User Related Events...
        'App\Events\Auth\Prooflo\UserRegistered' => [
            // 'App\Listeners\Prooflo\Subscription\CreateTrialEndingNotification',
        ],

        'App\Events\Prooflo\Subscription\UserSubscribed' => [
            'App\Listeners\Prooflo\Subscription\UpdateActiveSubscription',
            'App\Listeners\Prooflo\Subscription\UpdateTrialEndingDate',
        ],

        'App\Events\Prooflo\Profile\ContactInformationUpdated' => [
            'App\Listeners\Prooflo\Profile\UpdateContactInformationOnStripe',
        ],

        'App\Events\Prooflo\PaymentMethod\VatIdUpdated' => [
            'App\Listeners\Prooflo\Subscription\UpdateTaxPercentageOnStripe',
        ],

        'App\Events\Prooflo\PaymentMethod\BillingAddressUpdated' => [
            'App\Listeners\Prooflo\Subscription\UpdateTaxPercentageOnStripe',
        ],

        'App\Events\Prooflo\Subscription\SubscriptionUpdated' => [
            'App\Listeners\Prooflo\Subscription\UpdateActiveSubscription',
        ],

        'App\Events\Prooflo\Subscription\SubscriptionCancelled' => [
            'App\Listeners\Prooflo\Subscription\UpdateActiveSubscription',
        ],

        // Team Related Events...
        'App\Events\Prooflo\Teams\TeamCreated' => [
            //'App\Listeners\Prooflo\Teams\Subscription\CreateTrialEndingNotification',
        ],

        'App\Events\Prooflo\Teams\Subscription\TeamSubscribed' => [
            'App\Listeners\Prooflo\Teams\Subscription\UpdateActiveSubscription',
            'App\Listeners\Prooflo\Teams\Subscription\UpdateTrialEndingDate',
        ],

        'App\Events\Prooflo\Teams\Subscription\SubscriptionUpdated' => [
            'App\Listeners\Prooflo\Teams\Subscription\UpdateActiveSubscription',
        ],

        'App\Events\Prooflo\Teams\Subscription\SubscriptionCancelled' => [
            'App\Listeners\Prooflo\Teams\Subscription\UpdateActiveSubscription',
        ],

        'App\Events\Prooflo\Teams\UserInvitedToTeam' => [
            'App\Listeners\Prooflo\Teams\CreateInvitationNotification',
        ],
        'App\Events\Teams\UserInvitedToTeam' => [
            'App\Listeners\Teams\CreateInvitationNotification',
        ],

        'App\Events\PdfPageConverted' => [
            'App\Listeners\PdfPageConvertedListener'
        ],

        // Prooflo Events
        'App\Events\ProjectCreated' => [
            'App\Listeners\ProjectCreatedListener'
        ],

        // Prooflo Events
        'App\Events\InvitePepoleByLink' => [
            'App\Listeners\InvitePepoleByLinkListener'
        ],

        'App\Events\NewRevisionCreated' => [
            'App\Listeners\NewRevisionCreatedListener'
        ],

        'App\Events\CorrectionsSubmitted' => [
            'App\Listeners\CorrectionsSubmittedListener'
        ],

        'App\Events\ProjectApproved' => [
            'App\Listeners\ProjectApprovedListener'
        ],

        'App\Events\FinalFilesUploaded' => [
            'App\Listeners\FinalFilesUploadedListener'
        ],

        'App\Events\CommentPosted' => [
            'App\Listeners\CommentPostedListener'
        ],

        'App\Events\IssuePosted' => [
            'App\Listeners\IssuePostedListener'
        ],

        'App\Events\IssueStatusChanged' => [
            'App\Listeners\IssueStatusChangedListener'
        ],

        'App\Events\CustomerInvited' => [
            'App\Listeners\CustomerInvitedListener'
        ],

        'App\Events\NotificationRealTime' => [
            'App\Listeners\NotificationRealTimeListener'
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
