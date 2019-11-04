<?php

namespace Gjae\LaravelPayper\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Gjae\LaravelPayper\Models\PayperPayment;

use GuzzleHttp\Client;

use Gjae\LaravelPayper\Contracts\PaymentContract;
use Gjae\LaravelPayper\Contracts\GatewayInterface;
class PayperPaymentsController extends Controller
{

    private $paymentManagement = null;

    private $gateway = null;

    public function __construct(PaymentContract $payment)
    {
        $this->paymentManagement = $payment;
    }

    public function show_view()
    {
        return view('vendor.payper.payper_form');
    }


    public function make_payment(Request $request, GatewayInterface $gateway)
    {
        return $gateway->exec()->redirectTo(['reference' => $this->paymentManagement->reference ]);
    }

    public function success(Request $request)
    {
        return view('vendor.payper.success_transaction', ['transaction' => $this->paymentManagement]);
    }

    public function failure(Request $request)
    {
        return view('vendor.payper.failure_transaction');
    }

    public function pending(Request $request)
    {
        dd($this->paymentManagement);
    }
}
