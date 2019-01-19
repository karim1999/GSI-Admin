<?php

namespace App\Admin\Controllers;

use App\JointLectures;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\User;
use App\Lectures;
use App\PaymentMethod;

class JointLecturesController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new JointLectures);

        $grid->id('Id');
        $grid->user()->name('Student name');
        $grid->user()->phone();
        $grid->lectures_id('Lecture name')->display(function($lecturesId){
            return Lectures::find($lecturesId)->title;            
        });
        
        $grid->payments('Online')->display(function ($Onlines) {
            $last_value = 0;
            foreach($Onlines as $Online){
                $last_value += $Online['online'];
            }
            return $last_value;
            
        });

        $grid->column('Knet')->display(function () {
            $Knets = PaymentMethod::where('jointlecture_id', $this->id)->get();

            $last_value = 0;
            foreach($Knets as $Knet){
                $last_value += $Knet['knet'];
            }
            return $last_value;       
            
        });


        $grid->column('Cash')->display(function () {
            $Cashs = PaymentMethod::where('jointlecture_id', $this->id)->get();

            $last_value = 0;
            foreach($Cashs as $Cash){
                $last_value += $Cash['cash'];
            }
            return $last_value;       
            
        });
        
        // $grid->column('Debt')->display(function(){
        //     $lecture = Lectures::find($this->lectures_id)->price;
        //     return $this->amount - $lecture;
        // }); 

        $grid->column('Price')->display(function(){
            $lecture = Lectures::find($this->lectures_id)->price;
            return  $lecture;
        }); 

        $grid->type('Pay')->display(function ($gender) {
            if($gender == 1){
                return "<span class='label label-warning'>Before Attend</span>";
            }elseif($gender == 2){
                return "<span class='label label-warning'>After Attend</span>";
            }
        });

        
        $grid->filter(function($filter){
            
            // Remove the default id filter
            $filter->disableIdFilter();
            // Add a column filter
            // $filter->column(1/2, function ($filter) {
            //     $user = User::where('name', $filter);                
            //     $filter->$user->like('name');
            //     $filter->between('rate');
            // });
            
            // $filter->column(1/2, function ($filter) {
            //     $filter->equal('created_at')->datetime();
            //     $filter->between('updated_at')->datetime();
            //     $filter->equal('released')->radio([
            //         1 => 'YES',
            //         0 => 'NO',
            //     ]);
            // });
            
            // $filter->where(function ($query) {
            
            // $query->whereHas('user', function ($query) {
            //     $query->where('name', 'like', "%{$this->input}%")->orWhere('phone', 'like', "%{$this->input}%");
            // });
            
            // }, 'Student name or phone');

            // $filter->where(function ($query) {
                
            // $query->whereHas('lectures', function ($query) {
            //     $query->where('title', 'like', "%{$this->input}%");
            // });
            // }, 'lecture name');
    
            $filter->in('user_id')->multipleSelect(User::all()->pluck("name", "id"));
            $filter->in('lectures_id')->multipleSelect(Lectures::all()->pluck("title","id"));
            
            });

            

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(JointLectures::findOrFail($id));

        $show->id('Id');
        $show->user_id('User name')->as(function($userId) {
            return User::find($userId)->name;
        });
        $show->lectures_id('Lecture name')->as(function($lectureId) {
            return Lectures::find($lectureId)->title;
        });
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new JointLectures);

        $form->hasMany('payments', function (Form\NestedForm $form) {
            $form->number('online');  
            $form->number('cash');   
            $form->number('knet');

        });
         
        return $form;
    }
        
}
