<?php

namespace App\Exports;

use App\User;
use App\Lectures;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Lectures::where('user_id','=',auth()->user()->id)->select('title', 'gender', 'price', 'type_course', 
        'allowed', 'payment', 'start_date', 'start_time', 'end_time', 'user_id')
        ->get()->map(function($lecture){
            if($lecture->gender == 1){
                $lecture->gender = 'male';
            }else if($lecture->gender == 2){
                $lecture->gender = 'female';
            }else if($lecture->gender == 3){
                $lecture->gender = 'Both';
            }
            if($lecture->type_course == 1){
                $lecture->type_course = 'college';
            }else if($lecture->type_course == 2){
                $lecture->type_course = 'general';
            }
            if($lecture->payment == 1){
                $lecture->payment = 'Before Attend';
            }else if($lecture->payment == 2){
                $lecture->payment = 'After Attend';
            }
            
            $lecture->username = User::find($lecture->user_id)->name;
            return $lecture;
        });
        // return auth()->user()->lecture;
    }

    public function headings():array
    {
        return [
            'Title',
            'Gender',
            'Price',
            'Type course',
            'Allowed',
            'Payment',
            'Date',
            'Start Time',
            'End Time',
            'User_id',
            'User name',
        ];
    }
}
