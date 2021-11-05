<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Course;
use App\Models\Review;
use App\Models\MyCourse;

class ReviewController extends Controller
{
    public function create(Request $request) 
    {
        $data = $request->all();

        $formRequest = new ReviewRequest();

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

        $ifUserBelongsToCourse = MyCourse::where('course_id', '=', $course_id)
                                        ->where('user_id', '=', $user_id)
                                        ->first();
        
        if (empty($ifUserBelongsToCourse)) {
            return response()->json([
                'status' => 'error',
                'message' => 'user does not have this course!'
            ], 409);
        }

        $isExistsReview = Review::where('course_id', '=', $course_id)
                                ->where('user_id', '=', $user_id)
                                ->exists();

        if ($isExistsReview) {
            return response()->json([
                'status' => 'error',
                'message' => 'user already post review for this course'
            ], 409);
        }        

        $review = Review::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $review
        ]);
    }

    public function update(Request $request, $id) 
    {
        $formRequest = [
            'rating' => 'integer|min:1|max:5',
            'note' => 'string'
        ];

        $data = $request->except('user_id', 'course_id');

        $validate = Validator::make($data, $formRequest);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors()
            ], 400);
        }

        $review = Review::find($id);

        if (!$review) { 
            return response([
                'status' => 'error',
                'message' => 'review not found'
            ], 404);
        }

        $review->update($data);

        return response()->json([
            'status' => 'success',
            'data' => $review
        ]);

    }

    public function destroy($id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'status' => 'error',
                'message' => 'review not found'
            ], 404);
        }

        $review->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'review have been deleted'
        ]);
    }
}
