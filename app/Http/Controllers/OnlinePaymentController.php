<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use App\Http\Controllers\Components\KNet\OnlinePaymentIntegration;
use App\Http\Controllers\Components\RobustCMS;

class OnlinePaymentController extends BackendController
{

    public function anyIndex(Request $request){
        if($request->has('go') && $request->go != ''){
            $request->request->add([
                'user_id' => session('backendUser')->id,
                'table_id' => '20',
                'type' => 'test',
                'amount' => 10,
            ]);
            return OnlinePaymentIntegration::Buy($request, $amount = 10);
        }
        die('View');
    }

    public function getSuccess(Request $request){
        $request->request->add([
            'user_id' => $request->UDF1,
            'table_id' => $request->UDF2,
            'type' => $request->UDF3,
            'amount' => $request->UDF4,
        ]);

        $this->transaction($request);

        if($request->type == 'deferred')
            return $this->deferred($request);

        if($request->type == 'join-us')
            return $this->joinUs($request);

        if($request->type == 'wallet')
            return $this->wallet($request);
    }

    private function deferred($request){
        $deferred = \App\Models\StudentsDeferred::where('id', $request->table_id)->where('student_id', $request->user_id)->first();
        if(!is_object($deferred))
            return \App::abort(404);

        $lecturesStudents = \App\Models\LecturesStudents::find($deferred->lectures_students_id);
        if(!is_object($lecturesStudents))
            return \App::abort(404);

        $lecturesStudents->k_net = $request->amount;
        $lecturesStudents->defer = 0;
        $lecturesStudents->save();

        RobustCMS::removeDeferred($request->user_id, $request->amount);

        $deferred->delete();

//        $this->setPoints($request->user_id, $request->amount, $deferred->lecture_id, $deferred->teacher_id);

        $data['url'] = url('backend/dashboard');
        $data['status'] = 'success';
        $data['message'] = Lang::get('messages.KnetMessageSuccess');
        return view('backend.online_payment.index', $data);
    }

    private function joinUs($request){
        $student = new \App\Models\LecturesStudents;
        $student->lectures_id = $request->table_id;
        $student->student_id = $request->user_id;
        $student->price = $request->amount;
        $student->k_net = $request->amount;
        if(!$student->validate()){
            foreach($student->errors() as $error){
                $errors[] = Lang::get('messages.' . $error);
            }
            $data['url'] = url('backend/lectures/details/' . $request->table_id);
            $data['status'] = 'error';
            $data['message'] = \Session::flash('validate_errors', $errors);
            return view('backend.online_payment.index', $data);
        }
        $student->save();

        $teacher_id = null;
        if(is_object($student->lecture))
            $teacher_id = $student->lecture->teacher_id;

        $this->setPoints($request->user_id, $request->amount, $student->lectures_id, $teacher_id);

        $data['url'] = url('backend/lectures/details/' . $request->table_id);
        $data['status'] = 'success';
        $data['message'] = Lang::get('messages.KnetMessageSuccess');
        return view('backend.online_payment.index', $data);
    }

    private function wallet($request){
        $request->request->add(['student_id' => $request->user_id]);

        $return = WalletController::store($request, new \App\Models\StudentsWallet);
        if($return == TRUE){
            //$this->setPoints($request->user_id, $request->amount, null, null);
            $data['url'] = url('backend/wallet');
            $data['status'] = 'success';
            $data['message'] = Lang::get('messages.KnetMessageSuccess');
            return view('backend.online_payment.index', $data);
        }
    }

    public function getError(Request $request){
        $request->request->add([
            'user_id' => $request->UDF1,
            'table_id' => $request->UDF2,
            'type' => $request->UDF3,
            'amount' => $request->UDF4,
        ]);

        $this->transaction($request);

        if(!$request->method('POST'))
            return redirect('backend/dashboard');

        if($request->type == 'deferred')
            $data['url'] = url('backend/dashboard');

        if($request->type == 'join-us')
            $data['url'] = url('backend/lectures/details/' . $request->table_id);

        if($request->type == 'wallet')
            $data['url'] = url('backend/wallet');

        $data['status'] = 'error';
        $data['message'] = Lang::get('messages.KnetMessageError');

        return view('backend.online_payment.index', $data);
    }

    private function transaction($request){
        $json_encode = json_encode($request->all());
        $payment = new \App\Models\PaymentTransactions;
        $payment->transactions = $json_encode;
        $payment->created_by = $request->user_id;
        $payment->save();
    }

    private function setPoints($user_id, $amount, $lectures_id, $teacher_id){
        $points = RobustCMS::setCalculationPoints($amount);
        $point = new \App\Models\StudentsPoints;
        $point->student_id = $user_id;
        $point->lecture_id = $lectures_id;
        $point->teacher_id = $teacher_id;
        $point->point = $points;
        $point->save();
        RobustCMS::setPoints($user_id, $points);
    }

}
