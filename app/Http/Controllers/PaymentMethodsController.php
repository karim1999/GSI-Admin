<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use App\Models\Lectures;
use DB;
use App\Http\Controllers\Components\RobustCMS;

class PaymentMethodsController extends BackendController
{

    public function anyIndex(Request $request){
        $data['object'] = new \App\Models\Lectures;

        $result = \App\Models\Lectures::orderBy('id', 'ASC');

        if($request->has('lectures_id') && $request->lectures_id != ''){
            $lectures_id = strip_tags($request->lectures_id);
            $result->where('id', $lectures_id);
            $data['object'] = \App\Models\Lectures::find($lectures_id);
        }
        $result = $result->get();

        $resultArr = [];
        foreach($result as $row){
            $fromTime = date('h:i:s', strtotime($row->from_time));
            $toTime = date('h:i:s', strtotime($row->to_time));
            if(date('Y-m-d') <= $row->date)
                $color = '#257e4a';
            else
                $color = '#DA4453';
            $url = url('backend/payment-methods?lectures_id=' . $row->id . '&date=' . $row->date);
            $resultArr[] = [
                'id' => $row->id,
                'title' => $row->name,
                'start' => $row->date . 'T' . $fromTime . '-05:00:00',
                'end' => $row->date . 'T' . $toTime . '-05:00:00',
                'color' => $color,
                'url' => $url,
            ];
        }

        $data['lectures'] = json_encode($resultArr);

        return view('backend.payment_methods.index_1', $data);
    }

    public function anyPaid(Request $request){
        $id = $request->route()->parameters()['id'];
        if($request->method('POST')){
            DB::beginTransaction();
            try {

                $object = \App\Models\LecturesStudents::find($id);
                if(!is_object($object)){
                    throw new \Exception(Lang::get('messages.WrongObject'));
                }

                $amount = $this->getAmount($request);
                if($request->price < $amount){
                    throw new \Exception(Lang::get('messages.WrongAmount'));
                }

                $object->price = $request->price;
                $object->cash = $request->cash;
                $object->card_number = $request->card_number;
                $object->card = $request->card;
                $object->comment_admin = $request->comment;

                /* Points */
                if($request->has('points') && $request->points != 0 && $request->points != ''){
                    $points = RobustCMS::getPoints($object->student_id);
                    if($points == 0){
                        throw new \Exception(Lang::get('messages.EmptyPoints'));
                    }
                    if($points < $request->points){
                        throw new \Exception(Lang::get('messages.PointsNotEnough'));
                    }

                    $amount = $this->getAmount($request);
                    if($request->price < $amount){
                        throw new \Exception(Lang::get('messages.WrongAmount'));
                    }
                    RobustCMS::removePoints($object->student_id, $request->points);
                    $object->points = $request->points;
                }
                
                if($object->lecture->date == date('Y-m-d')){
                    $amount = $this->getAmount($request);
                    $points = RobustCMS::setCalculationPoints($amount);
                    if($object->student_id != 0){
                        $teacher_id = 0;
                        if(is_object($object->lecture))
                            $teacher_id = $object->lecture->teacher_id;

                        $point = new \App\Models\StudentsPoints;
                        $point->student_id = $object->student_id;
                        $point->lecture_id = $object->lectures_id;
                        $point->teacher_id = $teacher_id;
                        $point->point = $points;
                        if(!$point->validate()){
                            foreach($point->errors() as $error){
                                $errors[] = Lang::get('messages.' . $error);
                            }
                            $errors_em = implode('<br>', $errors);
                            throw new \Exception($errors_em);
                        }
                        $point->save();
                        RobustCMS::setPoints($object->student_id, $points);
                    }
                }

                /* Wallet */
                if($request->has('wallet') && $request->wallet != 0 && $request->wallet != ''){
                    $wallet = RobustCMS::getMoneyWallet($object->student_id);
                    if($wallet == 0){
                        throw new \Exception(Lang::get('messages.EmptyWallet'));
                    }
                    if($wallet < $request->wallet){
                        throw new \Exception(Lang::get('messages.WalletNotEnough'));
                    }
                    RobustCMS::removeMoneyWallet($object->student_id, $request->wallet);
                    $object->wallet = $request->wallet;
                }

                /* Defer */
                $amount = $this->getAmount($request);
                $amount = $amount + $request->defer;
                if($amount != $request->price){
                    throw new \Exception(Lang::get('messages.DeferredIsWrong'));
                }
                if($request->has('defer') && $request->defer != 0 && $request->defer != ''){
                    if($object->student_id != 0){
                        $teacher_id = 0;
                        if(is_object($object->lecture))
                            $teacher_id = $object->lecture->teacher_id;

                        $defer = new \App\Models\StudentsDeferred;
                        $defer->lectures_students_id = $object->id;
                        $defer->student_id = $object->student_id;
                        $defer->lecture_id = $object->lectures_id;
                        $defer->teacher_id = $teacher_id;
                        $defer->defer = $request->defer;
                        if(!$defer->validate()){
                            foreach($defer->errors() as $error){
                                $errors[] = Lang::get('messages.' . $error);
                            }
                            $errors_em = implode('<br>', $errors);
                            throw new \Exception($errors_em);
                        }
                        $defer->save();
                        RobustCMS::setDeferred($object->student_id, $request->defer);
                    }
                }
                $object->defer = $request->defer;

                if(!$object->validate()){
                    foreach($object->errors() as $error){
                        $errors[] = Lang::get('messages.' . $error);
                    }
                    $errors_em = implode('<br>', $errors);
                    throw new \Exception($errors_em);
                }

                $object->save();

                DB::commit();
                $response = new \stdClass();
                $response->status = 'Ok';
                $response->message = Lang::get('messages.Updatesuccessfully');
                return json_encode($response);
            } catch(\Exception $exception){
                DB::rollBack();
                $response = new \stdClass();
                $response->status = 'Error';
                $response->message = $exception->getMessage();
                return json_encode($response);
            }
        }
    }

    private function getAmount($request){
        $total = $request->cash + $request->card + $request->wallet;
        if($request->has('points') && $request->points != 0 && $request->points != ''){
            $money = RobustCMS::getCalculationPoints($request->points);
            $total = $total + $money;
        }
        return $total;
    }

}
