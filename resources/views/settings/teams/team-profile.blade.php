<spark-team-profile :user="user" :team="team" inline-template>
    <div>
        <div v-if="user && team">
            <!-- Update Team Photo -->
            @include('settings.teams.update-team-photo')

            <!-- Update Team Name -->
            @include('settings.teams.update-team-name')
        </div>
    </div>
</spark-team-profile>
