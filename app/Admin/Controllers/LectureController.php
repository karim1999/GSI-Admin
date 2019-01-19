<?php

namespace App\Admin\Controllers;

use App\Lectures;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\User;
use App\Courses;

class LectureController extends Controller
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
        $grid = new Grid(new Lectures);

        $grid->id('Id');
        $grid->user_id('Username')->display(function($userId) {
            return User::find($userId)->name;
        });
        $grid->title('Title');
        $grid->price('Price');
        $grid->type_course('Type course')->display(function ($type) {
            if($type == 1){
                return "<span class='label label-warning'>College</span>";
            }else{
                return "<span class='label label-warning'>General</span>";
            }
        });
        $grid->gender('Gender')->display(function ($gender) {
            if($gender == 0){
                return "<span class='label label-warning'>Male</span>";
            }elseif($gender == 1){
                return "<span class='label label-warning'>Female</span>";
            }else{
                return "<span class='label label-warning'>Both</span>";
            }
        });
        $grid->allowed('Num students');
        $grid->description('Description');
        $grid->start_duration('Start duraton');
        $grid->end_duration('End duraton');

        $grid->filter(function($filter){
            
            // Remove the default id filter
            $filter->disableIdFilter();
        
            // Add a column filter
            $filter->like('title', 'Title');
        
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
        $show = new Show(Lectures::findOrFail($id));

        $show->id('Id');
        $show->user_id('User id');
        $show->user_id('Username')->as(function($userId) {
            return User::find($userId)->name;
        });
        $show->title('Title');
        $show->price('Price');
        $show->type_course('Type course')->as(function($type) {
            if($type == 1){
                return "College";
            }else{
                return "General";
            }
        });
        $show->gender('Gender')->as(function ($gender) {
            if($gender == 1){
                return "Male";
            }elseif($gender == 2){
                return "Female";
            }else{
                return "Both";
            }
        });
        $show->allowed('Num students');
        $show->description('Description');
        $show->start_duration('Start duraton');
        $show->end_duration('End duraton');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
         $form = new Form(new Lectures);

        $form->select("user_id", "User name")->options(User::where('type', 2)->pluck('name', 'id'));
        $form->text('title', 'Title');
        $form->number('price', 'Price');
        $Type= Array(1 => "College",2 => "General");        
        $form->select('type_course', 'Type course')->options($Type)->rules('required');
        $Gender= Array(1 => "Male",2 => "Female",3 => "Both");
        $form->select("gender")->options($Gender)->rules('required');
        $form->number('num_students', 'Num students');
        $form->image('img', 'Img');
        $form->textarea('description', 'Description');
        $form->date('start_date');
        $form->date('end_date');
        $form->time('start_time');
        $form->time('end_time');
        $column = $form->time('start_date').$form->time('end_time');
        $column1 = $form->time('end_date').$form->time('end_time');
        $form->hidden('start_duration', $column);
        $form->hidden('end_duration', $column1);

        return $form;
    }
}
