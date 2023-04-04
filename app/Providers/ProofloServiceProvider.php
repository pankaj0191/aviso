<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ProofloServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'App\Contracts\IProjectService',
            'App\Services\ProjectService'
        );

        $this->app->bind(
            'App\Contracts\IProjectTimerService',
            'App\Services\ProjectTimerService'
        );

        $this->app->bind(
            'App\Contracts\IClientRequestService',
            'App\Services\ClientRequestService'
        );

        $this->app->bind(
            'App\Contracts\IProfileService',
            'App\Services\ProfileService'
        );

        $this->app->bind(
            'App\Contracts\IFileService',
            'App\Services\FileService'
        );

        $this->app->bind(
            'App\Contracts\IProofService',
            'App\Services\ProofService'
        );

        $this->app->bind(
            'App\Contracts\IRevisionService',
            'App\Services\RevisionService'
        );

        $this->app->bind(
            'App\Contracts\IEmailNotificationSettingsService',
            'App\Services\EmailNotificationSettingsService'
        );

        $this->app->bind(
            'App\Contracts\INotificationService',
            'App\Services\NotificationService'
        );

        $this->app->bind(
            'App\Contracts\API\V1\IAPIProjectService',
            'App\Services\API\V1\APIProjectProjectService'
        );

        $this->app->bind(
            'App\Contracts\API\V1\IAPIAuthService',
            'App\Services\API\V1\APIAuthService'
        );

        $this->app->bind(
            'App\Contracts\IUserService',
            'App\Services\UserService'
        );

        $this->app->bind(
            'App\Contracts\IUnreadCommentsService',
            'App\Services\UnreadCommentsService'
        );

        $this->app->bind(
            'App\Contracts\ITeamService',
            'App\Services\TeamService'
        );

        $this->app->bind(
            'App\Contracts\IPlanService',
            'App\Services\PlanService'
        );

        $this->app->bind(
            'App\Contracts\IStripeService',
            'App\Services\StripeService'
        );

        $this->app->bind(
            'App\Contracts\IBootstrapService',
            'App\Services\BootstrapService'
        );

        $this->app->bind(
            'App\Contracts\Admin\IUserService',
            'App\Services\Admin\UserService'
        );

        $this->app->bind(
            'App\Contracts\Admin\IProjectService',
            'App\Services\Admin\ProjectService'
        );

        $this->app->bind(
            'App\Contracts\ISlackService',
            'App\Services\SlackService'
        );

        $this->app->bind(
            'App\Contracts\AnnouncementRepository',
            'App\Services\AnnouncementRepository'
        );

        $this->app->bind(
            'App\Contracts\TeamRepository',
            'App\Repositories\TeamRepository'
        );
        $this->app->bind(
            'App\Contracts\Repositories\TeamRepository',
            'App\Repositories\TeamRepository'
        );
        $this->app->bind(
            'App\Contracts\UserRepository',
            'App\Repositories\UserRepository'
        );
        $this->app->bind(
            'App\Contracts\InitialFrontendState',
            'App\InitialFrontendState'
        );
        $this->app->bind(
            'App\Contracts\Repositories\NotificationRepository',
            'App\Repositories\NotificationRepository'
        );



        $this->app->bind(
            'App\Contracts\Interactions\Settings\Profile\UpdateProfilePhoto',
            'App\Interactions\Settings\Profile\UpdateProfilePhoto'
        );
        $this->app->bind(
            'App\Contracts\Interactions\Settings\Profile\UpdateContactInformation',
            'App\Interactions\Settings\Profile\UpdateContactInformation'
        );
        $this->app->bind(
            'App\Contracts\Interactions\Settings\Teams\CreateTeam',
            'App\Interactions\Settings\Teams\CreateTeam'
        );

        $this->app->bind(
            'App\Contracts\Interactions\Settings\Teams\AddTeamMember',
            'App\Interactions\Settings\Teams\AddTeamMember'
        );
        $this->app->bind(
            'App\Contracts\Interactions\Settings\Teams\UpdateTeamMember',
            'App\Interactions\Settings\Teams\UpdateTeamMember'
        );
        $this->app->bind(
            'App\Contracts\Interactions\Settings\Teams\UpdateTeamPhoto',
            'App\Interactions\Settings\Teams\UpdateTeamPhoto'
        );
        $this->app->bind(
            'App\Contracts\Interactions\Settings\Teams\SendInvitation',
            'App\Interactions\Settings\Teams\SendInvitation'
        );
        $this->app->bind(
            'App\Contracts\Interactions\Settings\Security\EnableTwoFactorAuth',
            'App\Interactions\Settings\Security\EnableTwoFactorAuthUsingAuthy'
        );
        $this->app->bind(
            'App\Contracts\Interactions\Settings\Security\VerifyTwoFactorAuthToken',
            'App\Interactions\Settings\Security\VerifyTwoFactorAuthTokenUsingAuthy'
        );
        $this->app->bind(
            'App\Contracts\Interactions\Settings\Security\DisableTwoFactorAuth',
            'App\Interactions\Settings\Security\DisableTwoFactorAuthUsingAuthy'
        );
        $this->app->bind(
            'App\Contracts\Interactions\Settings\Security\DisableTwoFactorAuth',
            'App\Interactions\Settings\Security\DisableTwoFactorAuthUsingAuthy'
        );
        $this->app->bind(
            'App\Contracts\Interactions\Settings\PaymentMethod\UpdatePaymentMethod',
            'App\Interactions\Settings\PaymentMethod\UpdateStripePaymentMethod'
        );
        $this->app->bind(
            'App\Contracts\Interactions\Settings\PaymentMethod\RedeemCoupon',
            'App\Interactions\Settings\PaymentMethod\RedeemStripeCoupon'
        );


    }
}
