<spark-teams :user="user" :teams="teams" inline-template>
    <div>
        <!-- Create Team -->
        @if (Spark::createsAdditionalTeams())
            @include('settings.teams.create-team')
        @endif

        <!-- Pending Invitations -->
        @include('settings.teams.pending-invitations')

        <!-- Current Teams -->
        <div v-if="user && teams.length > 0">
            @include('settings.teams.current-teams')
        </div>
    </div>
</spark-teams>
