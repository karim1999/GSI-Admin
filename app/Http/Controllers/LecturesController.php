<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lectures;
use App\Notification;
use App\JointLectures;
use App\User;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use Carbon\Carbon;

class LecturesController extends Controller
{
    public function showLecturesByDate(){
        return Lectures::all()->groupBy('start_date');
    }

    public function showLectures(){
        return Lectures::all();
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

    // public function notification(){
    //     // $notify = Notification::create
    //     // (
    //     //     [
    //     //         'user_id' => auth()->user(),
    //     //         'text' => 'new Norification .',
    //     //     ]
    //     // );
    //     $text = 'hello';
    //     $token = auth()->user()->token;
    
        
    //     return User::send($token,$text);
    // }

    public function notification(){
        
        // $start_duration = Lectures::all()->pluck('start_duration');
        // $timestamp = array();
        // $current_time = array();
        // foreach($start_duration as $start){
        //     $timestamp[] = strtotime($start) - strtotime('now');
        // }
        // foreach ($timestamp as $time){
        //     $current_time[] = $time/60/60;
        // }
        // return $current_time;

        // $start_duration = Lectures::all()->pluck('start_duration');
        // $timestamp = array();
        // $date = array();
        // foreach($start_duration as $start){
        //         $timestamp[] = Carbon::parse( $start)->toDateString();
        //     }
        // foreach ($timestamp as $time){
        //     $date[] = date('Y-m-d', strtotime('+1 month', strtotime($time)));
        // }
        // return $date;
        
        $balance = auth()->user()->balance;
        return $balance;

        if($balance < 0 ){

        }else{
            
        }

        // $lecture = Lectures::all()->pluck('id');
        
        // $price = Lectures::all()->pluck('price');
        // // $amount = JointLectures::pluck('amount')->where('lecture_id', 2);
        // $user = auth()->user()->jointLectures('amount');
        // return $user;
        
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        // $start = Lectures::first()->start_duration;
        // $timestamp = strtotime($start);
        // $time = date('H', $timestamp);
        // return $time - 24 Carbon::parse($start)->toTimeString();
        
        $notificationBuilder = new PayloadNotificationBuilder('my title');
        $notificationBuilder->setBody('Hello world')
                            ->setSound('default');
        
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['a_data' => 'my_data']);
        
        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();
        
        // You must change it to get your tokens
        $tokens = User::pluck('token')->toArray();
        
        $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
        
        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();
        
        //return Array - you must remove all this tokens in your database
        $downstreamResponse->tokensToDelete();
        
        //return Array (key : oldToken, value : new token - you must change the token in your database )
        $downstreamResponse->tokensToModify();
        
        //return Array - you should try to resend the message to the tokens in the array
        $downstreamResponse->tokensToRetry();
        
        // return Array (key:token, value:errror) - in production you should remove from your database the tokens present in this array
        $downstreamResponse->tokensWithError();

    }
}
