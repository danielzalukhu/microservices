<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChapterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Chapter;
use App\Models\Course;

class ChapterController extends Controller
{
    public function index(Request $request)
    {
        $chapters = Chapter::query();

        $course_id   = $request->query('course_id');

        $chapters->when($course_id, function($query) use ($course_id) {
            return $query->where("course_id", '=', $course_id);
        });

        return response()->json([
            'status' => 'success',
            'data' => $chapters->get()
        ]); 
    }

    public function create(Request $request)
    {
        $data = $request->all();

        $formRequest = new ChapterRequest();

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

        $chapter = Chapter::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $chapter
        ]);        
    }

    public function show($id)
    {
        $chapter = Chapter::find($id);

        if (!$chapter) {
            return response()->json([
                'status' => 'error',
                'message' => 'chapter not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $chapter
        ]);   
    }

    public function update(Request $request, $id)
    {
        $formRequest = [
            'name' => 'string',
            'course_id' => 'integer'
        ];

        $data = $request->all();

        $validate = Validator::make($data, $formRequest);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors()
            ], 400);
        }

        $chapter = Chapter::find($id);

        if (!$chapter) {
            return response()->json([
                'status' => 'error',
                'message' => 'chapter not found'
            ], 404);
        }

        $course_id = $request->input('course_id');

        if ($course_id) {
            $course = Course::find($course_id);
            if (!$course) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'course not found'
                ], 404);
            }
        }

        $chapter->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $chapter
        ]);
    }

    public function destroy($id)
    {
        $chapter = Chapter::find($id);

        if (!$chapter) {
            return response()->json([
                'status' => 'error',
                'message' => 'chapter not found'
            ], 404);
        }

        $chapter->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'chapter ' . $chapter->name . ' have been deleted'
        ]);
    }
}
