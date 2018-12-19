<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lectures;
use App\JointLectures;
use App\User;

class LecturesController extends Controller
{
    public function showLecturesByDate(){
        return Lectures::all()->groupBy('start_date');
    }

    public function showLectures(){
        return auth()->user()->lecture;
    }

    public function jointLecture(Lectures $lecture){
        auth()->user()->jointLectures()->attach($lecture);
        return auth()->user()->jointLectures;
    }

    public function addLecture(Request $request){
        $lecture = new Lectures;
        $lecture->title = $request->title;
        $lecture->subject = $request->subject;
        $lecture->price = $request->price;
        $lecture->type_course = $request->type_course;
        $lecture->gender = $request->gender;
        $lecture->attendance = $request->attendance;
        $lecture->allowed = $request->allowed;

        // $path = $request->file('img')->store('public/images');
        // $path= str_replace("public/","",$path);
        // $lecture->img= $path;

        $lecture->description = $request->description;
        $lecture->start_duration = $request->start_duration;
        $lecture->end_duration = $request->end_duration;
        $lecture->start_date = $request->start_date;

        $result= auth()->user()->lecture()->save($lecture);
        return response()->json($result);

    }

    public function jointLectureUsers(Lectures $lecture,User $user){
        $user->jointLectures()->attach($lecture);
        return $user;
    }

    public function editLecture(Lectures $lecture, Request $request){
        $lecture->title = $request->title;
        $lecture->subject = $request->subject;
        $lecture->price = $request->price;
        $lecture->type_course = $request->type_course;
        $lecture->gender = $request->gender;
        $lecture->allowed = $request->allowed;
        // $lecture->img = $request->img;
        $lecture->description = $request->description;
        $lecture->start_duration = $request->start_duration;
        $lecture->end_duration = $request->end_duration;

        auth()->user()->lecture()->save($lecture);
        return response()->json($lecture);
    }

    public function deleteLecture(Lectures $lecture){
        $lecture->delete();
        return $lecture;
    }

    public function addLectureUsers(){
        return User::all();
    }

    public function getUsers(){
        return auth()->user()->lecture()->with('jointUsers')->get();
    }
}
