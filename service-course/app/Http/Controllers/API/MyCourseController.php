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

        $validate = Validator::make($data, $formRequest::rules());

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors()
            ], 400);
        }

        $course_id = $request->input('course_id');

        $course = Course::find($course_id);

        if(!$course) {
            return response()->json([
                'stattus' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        $user_id = $request->input('user_id');

        // Panggil function dari helpers
        $user = getUser($user_id);

        if ($user['status'] === 'error') {
            return response()->json([
                'status' => $user['status'],
                'message' => $user['message'],
            ], $user['http_code']);
        }

        // Cek duplicate data

        $isExistsMyCourse = MyCourse::where('course_id', '=', $course_id)
                                    ->where('user_id', '=', $user_id)
                                    ->exists();

        if ($isExistsMyCourse) {
            return response()->json([
                'status' => 'error',
                'message' => 'user already taken this course'
            ], 409);
        }

        $my_course = MyCourse::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $my_course
        ]);
    }
}
