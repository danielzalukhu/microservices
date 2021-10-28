<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LessonRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Chapter;

class LessonController extends Controller
{
    public function index(Request $request)
    {
        $lessons = Lesson::query();

        $chapter_id   = $request->query('chapter_id');

        $lessons->when($chapter_id, function($query) use ($chapter_id) {
            return $query->where("chapter_id", '=', $chapter_id);
        });

        return response()->json([
            'status' => 'success',
            'data' => $lessons->get()
        ]); 
    }

    public function create(Request $request)
    {
        $data = $request->all();

        $formRequest = new LessonRequest();

        $validator = Validator::make($data, $formRequest::rules());

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $chapter_id = $request->input('chapter_id');

        $chapter = Chapter::find($chapter_id);

        if (!$chapter) {
            return response()->json([
                'status' => 'error',
                'message' => 'chapter not found'
            ], 404);
        }

        $lesson = Lesson::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $lesson
        ]);
    }

    public function show($id) 
    {
        $lesson = Lesson::find($id);

        if(!$lesson) {
            return response()->json([
                'status' => 'error',
                'message' => 'lesson not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $lesson
        ]);
    }

    public function update(Request $request, $id)
    {
        $formRequest = [
            'name' => 'string',
            'video' => 'string',
            'chapter_id' => 'integer'
        ];

        $data = $request->all();

        $validate = Validator::make($data, $formRequest);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors()
            ], 400);
        }

        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json([
                'status' => 'error',
                'message' => 'lesson not found'
            ], 404);
        }

        $chapter_id = $request->input('chapter_id');

        if ($chapter_id) {
            $chapter = Chapter::find($chapter_id);
            if (!$chapter) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'chapter not found'
                ], 404);
            }
        }

        $lesson->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $lesson
        ]);
    }

    public function destroy($id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json([
                'status' => 'error',
                'message' => 'lesson not found'
            ], 404);
        }

        $lesson->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'lesson ' . $lesson->name . ' have been deleted'
        ]);
    }
}
