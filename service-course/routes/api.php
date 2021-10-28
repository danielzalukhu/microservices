<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Mentor
Route::get('mentor', 'API\MentorController@index');
Route::post('mentor', 'API\MentorController@create');
Route::get('mentor/{id}', 'API\MentorController@show');
Route::put('mentor/{id}', 'API\MentorController@update');
Route::delete('mentor/{id}', 'API\MentorController@destroy');

// Course
Route::get('course', 'API\CourseController@index');
Route::post('course', 'API\CourseController@create');
Route::get('course/{id}', 'API\CourseController@show');
Route::put('course/{id}', 'API\CourseController@update');
Route::delete('course/{id}', 'API\CourseController@destroy');

// Chapter
Route::get('chapter', 'API\ChapterController@index');
Route::post('chapter', 'API\ChapterController@create');
Route::get('chapter/{id}', 'API\ChapterController@show');
Route::put('chapter/{id}', 'API\ChapterController@update');
Route::delete('chapter/{id}', 'API\ChapterController@destroy');

// Lesson
Route::get('lesson', 'API\LessonController@index');
Route::post('lesson', 'API\LessonController@create');
Route::get('lesson/{id}', 'API\LessonController@show');
Route::put('lesson/{id}', 'API\LessonController@update');
Route::delete('lesson/{id}', 'API\LessonController@destroy');

// Image Course
Route::post('image_course', 'API\ImageCourseController@create');
Route::delete('image_course/{id}', 'API\ImageCourseController@destroy');

// My Course
Route::get('my_course', 'API\MyCourseController@index');
Route::post('my_course', 'API\MyCourseController@create');


