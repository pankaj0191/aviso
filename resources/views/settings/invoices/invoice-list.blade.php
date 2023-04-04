<spark-invoice-list :user="user" :team="team"
                    :invoices="invoices" :billable-type="billableType" inline-template>

    <div class="card card-default">
        <div class="card-header">{{__('Invoices')}}</div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                </thead>
                <tbody>
                <tr v-for="invoice in invoices">
                    <!-- Invoice Date -->
                    <td>
                        <strong>@{{ invoice.date | date }}</strong>
                    </td>

                    <!-- Invoice Total -->
                    <td>
                        @{{ invoice.total.toString().slice(0, -2) | currency }}
                    </td>

                    <!-- Invoice Download Button -->
                    <td class="text-right">
                        <a :href="invoice.invoice_pdf">
                            <button class="btn btn-default">
                                <i class="fa fa-btn fa-file-pdf-o"></i> {{__('Download PDF')}}
                            </button>
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</spark-invoice-list>
