<spark-team-membership :user="user" :team="team" :billable-type="billableType" inline-template>
    <div>
        @if (Auth::user()->ownsTeam($team))
            <!-- Send Invitation -->
            <div v-if="user && team">
                @include('settings.teams.send-invitation')
            </div>

            <!-- Pending Invitations -->
            <div v-if="invitations && invitations.length > 0">
                @include('settings.teams.mailed-invitations')
            </div>
        @endif

        <!-- Team Members -->
        <div v-if="user && team">
            @include('settings.teams.team-members')
        </div>
    </div>
</spark-team-membership>
