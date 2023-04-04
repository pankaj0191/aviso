<spark-security :user="user" inline-template>
	<div>
	    <!-- Update Password -->
	    @include('settings.security.update-password')

	    <!-- Two-Factor Authentication -->
	    @if (Spark::usesTwoFactorAuth())
	    	<div v-if="user && ! user.uses_two_factor_auth">
	    		@include('settings.security.enable-two-factor-auth')
	    	</div>

	    	<div v-if="user && user.uses_two_factor_auth">
	    		@include('settings.security.disable-two-factor-auth')
	    	</div>

			<!-- Two-Factor Reset Code Modal -->
	    	@include('settings.security.modals.two-factor-reset-code')
	    @endif
    </div>
</spark-security>
