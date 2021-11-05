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
use App\Models\Chapter;

use Log;

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
        //  chapter.lesson itu = ngambil data lesson dari model chapter yang saling berhubungan
        $course = Course::with('chapter.lesson')
        ->with('mentor')
        ->with('images')
        ->find($id);

        if (!$course) {
            return response()->json([
                'status' => 'error',
                'message' => 'course not found'
            ], 404);
        }

        $reviews = Review::where('course_id', '=', $id)->get()->toArray();      
        
        // Panggil service user -> untuk ambil data user/member yang memberi review pada table review di service course
        if (count($reviews) > 0) {
            // 1. ambil user_id dari array pada variable $reviews
            $user_ids = array_column($reviews, 'user_id');

            // 2. kumpulan user_id yang udh di dapet ambil detailnya dari service user
            $users = getUserByIds($user_ids);
            // echo "<pre>".print_r($users, 1)."</pre>";

            // 3. cek respon dari service user yang dipanggil
            if ($users['status'] === 'error') {
                // 4. kalau terjadi error, reviewnya []. (salah satu error adalah -> service user mati)
                $reviews = [];
                
                Log::debug('Service User (/) - RESPONSE API Data => ' . json_encode($users));
            } else {
                // 5. kalau service user berhasil ngasih response
                // combine data id-id user hasil API service user DENGAN id user_id yang di kumpulin ($user_ids) dari service course
                foreach($reviews as $key => $review) {            
                    // cari id user dari $reviews di dalam kumpulan array yang dikelompokan hasilnya dari service user   
                    // param 1 = ambil user_id dari array reviews                       
                    // param 2 = kelompok id user dari object2 dalam array hasil service user 
                    $userIndex = array_search($review['user_id'], array_column($users['data'], 'id'));
                    // echo "<pre>".print_r($userIndex, 1)."</pre>";   

                    $reviews[$key]['users'] = $users['data'][$userIndex]; 
                }
                
                Log::debug('Service User (/) - RESPONSE API Data => ' . json_encode($users));
            }
        }

        $total_student = MyCourse::where('course_id', '=', $id)->count();
        $total_video = Chapter::where('course_id', '=', $id)->withCount('lesson')->get()->toArray();
        $count_total_videos = array_sum(array_column($total_video, 'lesson_count'));
        // echo "<pre>".print_r($count_total_videos, 1)."</pre>";

        $course['reviews'] = $reviews;
        $course['total_video'] = $count_total_videos;
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
