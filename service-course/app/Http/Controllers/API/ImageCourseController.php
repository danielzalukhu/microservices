<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageCourseRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\ImageCourse;
use App\Models\Course;

class ImageCourseController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();

        $formRequest = new ImageCourseRequest();

        $validator = Validator::make($data, $formRequest::rules());

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $course_id = $request->input('course_id');

        $course = Course::find($course_id);

        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        $image_course = ImageCourse::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $image_course
        ]);
    }

    public function destroy($id)
    {
        $image_course = ImageCourse::find($id);

        if (!$image_course) {
            return response()->json([
                'status' => 'error',
                'message' => 'image course not found'
            ], 404);
        }

        $image_course->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'image course ' . $image_course->image . ' have been deleted'
        ]);
    }
}
