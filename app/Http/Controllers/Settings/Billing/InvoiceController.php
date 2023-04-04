<?php

namespace App\Http\Controllers\Settings\Billing;

use App\Spark;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Contracts\IStripeService;
use App\Http\Controllers\Controller;

class InvoiceController extends Controller
{
    /**
     * @var IStripeService
     */
    private $stripeService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(IStripeService $stripeService)
    {
        $this->middleware('auth');
        $this->stripeService = $stripeService;
    }

    /**
     * Get all of the invoices for the current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        if (! $request->user()->hasBillingProvider()) {
            return [];
        }

        $data = $this->stripeService->getCustomerInvoices($request->user());

        foreach ($data as $invoice) {
            $invoice->date = Carbon::parse($invoice->date)->toDateTimeString();
        }
        return response()->json([
            'data' => $data,
            'object' => 'list',
            'url' => 'v1/invoices'
        ]);
    }

    /**
     * Download the invoice with the given ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request, $id)
    {
        $invoice = $request->user()->localInvoices()
                            ->where('id', $id)->firstOrFail();

        return $request->user()->downloadInvoice(
            $invoice->provider_id, ['id' => $invoice->id] + Spark::invoiceDataFor($request->user())
        );
    }
}
