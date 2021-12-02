<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\MyCourseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Course;
use App\Models\MyCourse;

class MyCourseController extends Controller
{
    public function index(Request $request)
    {
        $my_courses = MyCourse::query()->with('course');

        $user_id   = $request->query('user_id');

        $my_courses->when($user_id, function($query) use ($user_id) {
            return $query->where("user_id", '=', $user_id);
        });

        return response()->json([
            'status' => 'success',
            'data' => $my_courses->get()
        ]);
    }

    public function create(Request $request)
    {
        $data = $request->all();        

        $formRequest = new MyCourseRequest();

        foreach($data as $key => $value) {                
            $validate = Validator::make($value, $formRequest::rules());
            if ($validate->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validate->errors()
                ], 400);
            }

            $course = Course::find($value['course_id']);
            if(!$course) {
                return response()->json([
                    'stattus' => 'error',
                    'message' => 'course not found'
                ], 404);
            }

            // Panggil function dari helpers
            $user = getUser($value['user_id']);
            if ($user['status'] === 'error') {
                return response()->json([
                    'status' => $user['status'],
                    'message' => $user['message'],
                ], $user['http_code']);
            }

            // Cek duplicate data
            $isExistsMyCourse = MyCourse::where('course_id', '=', $value['course_id'])
                                        ->where('user_id', '=', $value['user_id'])
                                        ->exists();

            if ($isExistsMyCourse) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'user already taken this course'
                ], 409);
            }

            if ($course->type === 'premium') {

                if ($course->price ===0) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $course->name . ' course price can not be null (0)'
                    ], 405);
                }

                $post_course[] = $course->toArray();                                    

            } else {
                $my_course[] = MyCourse::create($value);                     
            }
        }    

        /** Handle if class request is not premium */
        if ($course->type !== 'premium') {
            return response()->json([
                'status' => 'success',
                'data' => $my_course
            ]);
        }            

        /** Handle create premium class */
        $order = postOrder([
            'user' => $user['data'],
            'course' => $post_course
        ]);                    

        if ($order['status'] === 'error') {
            return response()->json([
                'status' => $order['status'],
                'message' => $order['message']
            ], $order['http_code']);
        }

        return response()->json([
            'status' => $order['status'],
            'data' => $order['data']
        ]);       
    }

    public function giveAccessPremiumClass(Request $request) 
    {
        $data = $request->all();

        foreach($data as $item) {
            $my_course = MyCourse::create($item);
        }

        return response()->json([
            'status' => 'success',
            'data' => $my_course
        ]);
    }
}
