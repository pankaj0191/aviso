<spark-subscription :user="user" :team="team" :billable-type="billableType" inline-template>
    <div>
        <div v-if="plans.length > 0">
            <!-- Trial Expiration Notice -->
            @include('settings.subscription.trial-expiration-notice')

            <!-- New Subscription -->
            <div v-if="needsSubscription">
                @include('settings.subscription.subscribe')
            </div>

            <!-- Update Subscription -->
            <div v-if="subscriptionIsActive">
                @include('settings.subscription.update-subscription')
            </div>

            <!-- Resume Subscription -->
            <div v-if="subscriptionIsOnGracePeriod">
                @include('settings.subscription.resume-subscription')
            </div>

            <!-- Cancel Subscription -->
            <div v-if="subscriptionIsActive">
                @include('settings.subscription.cancel-subscription')
            </div>
        </div>

        <!-- Plan Features Modal -->
        @include('modals.plan-details')
    </div>
</spark-subscription>
