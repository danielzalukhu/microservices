<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Order;

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

    /**   
     *  Logic seharusnya kirim 1 order ID bisa punya banyak detail 
     * (please luangin waktu update function ini)
     * ubah juga struktur db-nya untuk 1 order ID bisa punya lebih dari 1 Detail
     */
    /**
     * 1 Order ID harusnya nyimpen lebih dari 1 course_id, misal:
     * Table Order
     * id | status | user_id | snap_url | metadata 
     * 201 | pending | 2 | bayar.com | [{ objectCourse1 }, { objectCourse2 }]
     * ----------------------------------------------------------------------
     * Table Order Detail
     * id | order_id | course_id 
     * 1 | 201 | 1
     * 2 | 201 | 2
     */
    public function create_v2(Request $request)
    {
        $user = $request->input('user');
        $courses = $request->input('course');
        
        /* Create Order ID in database */
        foreach($courses as $key => $order) {
            $orders[] = Order::create([
                'user_id' => $user['id'],
                'course_id' => $order['id'],
            ]);

            $orders[$key]['course'] = $order;
        }        

        /** Prepare detail parameter need to send to midtrans  */
        $transaction_details = [];
        $item_details = [];

        foreach($orders as $order) {         
            array_push($transaction_details,  [
                'order_id' => $order->id,
                'gross_amount' => $order['course']['price']
            ]);

            array_push($item_details, [
                'id' => $order['course']['id'],
                'price' => $order['course']['price'],
                // quantity => order setiap kelas berapa banyak
                // case kali ini diasumsikan setiap order kelas masing2 jumlah 1
                // beda kalau misal yg dijual item kyk buah/apapun yg dibutuhkan lebih dari 1 perlu hitung
                'quantity' => 1,
                'name' => $order['course']['name'],
                // brand & category ini field optional 
                'brand' => 'BWA',
                'category' => 'Online Course'
            ]);
            
            $customer_details = [
                'first_name' => $user['name'],
                'email' => $user['email']
            ];
        }
        
        /** Set midtrans detail parameter */
        $midTransParams = [
            'transaction_details' => $transaction_details,
            'item_details' => $item_details,
            'customer_details' => $customer_details
        ];

        /** Send request to midtrans send the parameter to fetch the SNAP URL */
        $midTransSnapUrl = $this->getMidTransUrl($midTransParams);
        
        return response()->json([
            'status' => 'success',
            'data' => [
                $order,
                $midTransSnapUrl
            ]
        ]);
    }
}
