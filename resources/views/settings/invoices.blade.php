<spark-invoices :user="user" :team="team" :billable-type="billableType" inline-template>
    <div>
        <!-- Update Extra Billing Information -->
        <div v-if="billable">
            @include('settings.invoices.update-extra-billing-information')
        </div>

        <!-- Invoice List -->
        <div v-if="invoices.length > 0">
        	@include('settings.invoices.invoice-list')
        </div>
    </div>
</spark-invoices>
