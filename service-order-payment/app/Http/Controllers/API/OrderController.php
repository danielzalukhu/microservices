<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\OrderDetail;

class OrderController extends Controller
{
    private function getMidTransUrl($params) 
    {
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_PRODUCTION');
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = (bool) env('MIDTRANS_3DS');      
        
        $snapUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;
        return $snapUrl;
    }

    public function index(Request $request)
    {
        $user_id = $request->input('user_id');
        
        $orders = Order::query();

        $orders->when($user_id, function($query) use ($user_id) {
            return $query->where('user_id', '=', $user_id);
        });

        return response()->json([
            'status' => 'success',
            'data' => $orders->get()
        ]);
    }
    
    public function create(Request $request)
    {
        $user = $request->input('user');
        $courses = $request->input('course');

        /** Generate total price */
        $total_price = array_sum(array_column($courses, 'price'));

        $fields = [
            'total_price' => $total_price,
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'user_email' => $user['email']
        ];

        $order = Order::create($fields);
                
        $uuid = 'TRX' . mt_rand(10000, 99999) . mt_rand(100, 999);        
        
        /** Prepare detail parameter need to send to midtrans  */
        $transaction_details = [
            'order_id' => $uuid."-".$order->id,
            'gross_amount' => $total_price
        ];

        $item_details = [];

        foreach($courses as $course) {     
            $metadata[] = [
                'course_id' => $course['id'],
                'course_price' => $course['price'],
                'course_name' => $course['name'],
                'course_thumbnail' => $course['thumbnail'],
                'course_level' => $course['level']
            ];

            array_push($item_details, [
                'id' => $course['id'],
                'price' => $course['price'],
                'quantity' => 1,
                'name' => $course['name'],
                'brand' => 'BWA',
                'category' => 'Online Course'
            ]);        

            $details[] = new OrderDetail([
                'order_id' => $order->id,
                'course_id' => $course['id'],
                'price' => $course['price']
            ]);
        }

        $customer_details = [
            'first_name' => $user['name'],
            'email' => $user['email']
        ];
                
        /** Set midtrans detail parameter */
        $midTransParams = [
            'transaction_details' => $transaction_details,
            'item_details' => $item_details,
            'customer_details' => $customer_details
        ];

        /** Send request to midtrans send the parameter to fetch the SNAP URL */
        $midTransSnapUrl = $this->getMidTransUrl($midTransParams);
        
        /** Save order details */
        $order->details()->saveMany($details);
        
        /** Store snap_url, dan metadata ditiap kolomnya */
        $order->snap_url = $midTransSnapUrl;        
        $order->metadata = $metadata;

        $order->save();

        return response()->json([
            'status' => 'success',
            'data' => $order
        ]);
    }

    public function create_only_can_order_one_course(Request $request)
    {
        $user = $request->input('user');
        $course = $request->input('course');

        $order = Order::create([
            'user_id' => $user['id'],
            'course_id' => $course['id'],
        ]);

        /** Prepare detail parameter need to send to midtrans  */
        $transaction_details = [
            'order_id' =>  Str::random(5).'-'.$order->id,
            'gross_amount' => $course['price']
        ];

        $item_details = [
            [
                'id' => $course['id'],
                'price' => $course['price'],
                'quantity' => 1,
                'name' => $course['name'],
                // brand & category ini field optional 
                'brand' => 'BWA',
                'category' => 'Online Course'
            ]
        ];
        
        $customer_details = [
            'first_name' => $user['name'],
            'email' => $user['email']
        ];

        /** Set midtrans detail parameter */
        $midTransParams = [
            'transaction_details' => $transaction_details,
            'item_details' => $item_details,
            'customer_details' => $customer_details
        ];

        /** Send request to midtrans send the parameter to fetch the SNAP URL */
        $midTransSnapUrl = $this->getMidTransUrl($midTransParams);
        
        /** Store snap_url dan metadata ditiap kolomnya masing2 */
        $order->snap_url = $midTransSnapUrl;
        
        $order->metadata = [
            'course_id' => $course['id'],
            'course_price' => $course['price'],
            'course_name' => $course['name'],
            'course_thumbnail' => $course['thumbnail'],
            'course_level' => $course['level']
        ];

        $order->save();

        return response()->json([
            'status' => 'success',
            'data' => $order
        ]);
    }
}
