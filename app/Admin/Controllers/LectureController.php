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
        $grid->course_id('Course name')->display(function($courseId) {
            return Courses::find($courseId)->title;
        });
        $grid->title('Title');
        $grid->price('Price');
        $grid->type_course('Type course');
        $grid->gender('Gender')->display(function ($gender) {
            if($gender == 0){
                return "<span class='label label-warning'>Male</span>";
            }elseif($gender == 1){
                return "<span class='label label-warning'>Female</span>";
            }else{
                return "<span class='label label-warning'>Both</span>";
            }
        });
        $grid->num_students('Num students');
        $grid->description('Description');
        $grid->start_date('Start date');
        $grid->end_date('End date');

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
        $show->course_id('Course id');
        $show->user_id('Username')->as(function($userId) {
            return User::find($userId)->name;
        });
        $show->course_id('Course name')->as(function($courseId) {
            return Courses::find($courseId)->title;
        });
        $show->title('Title');
        $show->price('Price');
        $show->type_course('Type course');
        $show->gender('Gender')->as(function ($gender) {
            if($gender == 0){
                return "Male";
            }elseif($gender == 1){
                return "Female";
            }else{
                return "Both";
            }
        });
        $show->num_students('Num students');
        $show->description('Description');
        $show->start_date('Start date');
        $show->end_date('End date');

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

        $form->select("user_id", "Owner")->options(User::all()->pluck('name', 'id'));
        $form->select("course_id", "Course")->options(Courses::all()->pluck('title', 'id'));
        $form->text('title', 'Title');
        $form->number('price', 'Price');
        $form->text('type_course', 'Type course');
        $Gender= Array(0 => "Male",1 => "Female",2 => "Both");
        $form->select("gender")->options($Gender)->rules('required');
        $form->number('num_students', 'Num students');
        $form->image('img', 'Img');
        $form->textarea('description', 'Description');
        $form->date('start_date', 'Start date');
        $form->date('end_date', 'End date');

        return $form;
    }
}
