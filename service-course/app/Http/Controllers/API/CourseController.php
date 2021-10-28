<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Course;
use App\Models\Mentor;
use App\Models\Review;
use App\Models\MyCourse;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::query();

        $name   = $request->query('name');
        $status = $request->query('status');

        $courses->when($name, function($query) use ($name) {
            return $query->whereRaw("name LIKE '%" . strtolower($name) . "%'");
        });

        $courses->when($status, function($query) use ($status) {
            return $query->where("status", '=', $status);
        });

        return response()->json([
            'status' => 'success',
            'data' => $courses->paginate(10)
        ]);
    }

    public function create(Request $request)
    {        
        $data = $request->all();

        $formRequest = new CourseRequest();

        $validate = Validator::make($data, $formRequest::rules());

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors()
            ], 400);
        }

        $mentor_id = $request->input('mentor_id');
        
        $mentor = Mentor::find($mentor_id);

        if (!$mentor) {
            return response()->json([
                'status' => 'error',
                'message' => 'mentor not found'
            ], 404);
        }

        $course = Course::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $course
        ]);
    }

    public function show($id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        $reviews = Review::where('course_id', '=', $id)->get()->toArray();
        $total_student = MyCourse::where('course_id', '=', $id)->count();

        $course['reviews'] = $reviews;
        $course['total_student'] = $total_student;

        return response()->json([
            'status' => 'success',
            'data' => $course
        ]);
    }

    public function update(Request $request, $id)
    {
        $formRequest = [
            'name' => 'string',
            'certificate' => 'boolean',
            'thumbnail' => 'string|url',
            'type' => 'in:free,premium',
            'status' => 'in:draft,published',
            'price' => 'integer',
            'level' => 'in:all-level,beginner,intermediate,advanced',
            'mentor_id' => 'integer',
            'description' => 'string'
        ];

        $data = $request->all();

        $validate = Validator::make($data, $formRequest);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors()
            ], 400);
        }

        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        $mentor_id = $request->input('mentor_id');

        if ($mentor_id) {
            $mentor = Mentor::find($mentor_id);
            if (!$mentor) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'mentor not found'
                ], 404);
            }
        }

        $course->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $course
        ]);
    }

    public function destroy($id) 
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        $course->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'course ' . $course->name . ' have been deleted'
        ]);
    }
}
