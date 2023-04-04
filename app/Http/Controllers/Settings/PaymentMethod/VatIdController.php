<?php

namespace App\Http\Controllers\Settings\PaymentMethod;

use App\Spark;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Contracts\Repositories\UserRepository;

class VatIdController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Update the VAT ID for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'vat_id' => 'max:50|vat_id',
        ]);

        Spark::call(UserRepository::class.'@updateVatId', [
            $request->user(), $request->vat_id
        ]);
    }
}
