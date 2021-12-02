<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\PaymentLog;

class WebhookController extends Controller
{
    /** Webhook dari midtrans yang datang ke aplikasi kita */
    public function midTransHandler(Request $request)
    {
        $data               = $request->all();

        $signature_key      = $data['signature_key'];

        $order_id           = $data['order_id'];
        $status_code        = $data['status_code'];
        $gross_amount       = $data['gross_amount'];
        $server_key         = env('MIDTRANS_SERVER_KEY');

        /** signature_key formula: sha512(order_id+status_code+gross_amount+serverkey) */
        $my_signature_key   = hash('sha512', $order_id.$status_code.$gross_amount.$server_key);

        $transaction_status = $data['transaction_status'];
        $payment_type       = $data['payment_type'];
        $fraud_status       = $data['fraud_status'];

        /** Validasi incoming signature key dari midtrans dengan formula yang seharusnya */
        if ($signature_key !== $my_signature_key) {
            return response()->json([
                'status'    => 'error',
                'message'   => 'invalid signature key'
            ], 400);
        }

        $get_order_id = explode('-', $order_id);
        $order = Order::find($get_order_id[1]);

        if (!$order) {
            return response()->json([
                'status'    => 'error',
                'message'   => 'order id not found'
            ], 404);
        }

        /** Validasi kalo status di DB udh sukses kita gaboleh ngapa2in lagi */
        if ($order->status === 'success') {
            return response()->json([
                'status'    => 'error',
                'message'   => 'operation not permitted'
            ], 405);
        }

        /*
        * Example on Handling HTTP Notifications 
        * Validasi handler yang copas dari midtrans 
        */
        if ($transaction_status == 'capture'){
            if ($fraud_status == 'challenge'){
                // TODO set transaction status on your database to 'challenge'
                // and response with 200 OK
                $order->status = 'challenge';
            } else if ($fraud_status == 'accept'){
                // TODO set transaction status on your database to 'success'
                // and response with 200 OK
                $order->status = 'success';
            }
        } else if ($transaction_status == 'settlement'){
            // TODO set transaction status on your database to 'success'
            // and response with 200 OK
            $order->status = 'success';
        } else if ($transaction_status == 'cancel' ||
          $transaction_status == 'deny' ||
          $transaction_status == 'expire'){
          // TODO set transaction status on your database to 'failure'
          // and response with 200 OK
          $order->status = 'failure';
        } else if ($transaction_status == 'pending'){
          // TODO set transaction status on your database to 'pending' / waiting payment
          // and response with 200 OK
          $order->status = 'pending';
        }

        /** Create payment log into table payment_logs */
        $logs = [
            'status'        => $transaction_status,
            'raw_response'  => json_encode($data),
            'order_id'      => $get_order_id[1],
            'payment_type'  => $payment_type
        ];

        PaymentLog::create($logs);

        $get_order_detail = $order->details->toArray();
        $params = [];

        foreach($get_order_detail as $details) {            
            array_push($params, [
                'user_id' => $order->user_id,
                'course_id' => $details['course_id'],
            ]);
        }

        /** Update status order di table order id */
        $order->save();
                
        /** Give user access premiun to class if their payment is success */
        if ($order->status === 'success') {
            /** Kirim aksesnya ke service course */            
            giveAccessPremiumClass($params);
        }

        return response()->json('ok');
    }
}
