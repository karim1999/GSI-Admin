<?php

namespace App\Admin\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Hash;
use Admin;

class UserController extends Controller
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
        $grid = new Grid(new User);

        $grid->id('Id');
        $grid->name('Name');
        $grid->email('Email');
        $grid->type('Type')->display(function ($type) {
            if($type == 1){
                return "<span class='label label-warning'>Student</span>";
            }else{
                return "<span class='label label-warning'>Teacher</span>";
            }
        });
        $grid->phone('Phone');
        $grid->gender('Gender')->display(function ($gender) {
            if($gender == 1){
                return "<span class='label label-warning'>Male</span>";
            }else{
                return "<span class='label label-warning'>Female</span>";
            }
        });
        $grid->created_at('Created At');

        $grid->filter(function($filter){
            
            // Remove the default id filter
            $filter->disableIdFilter();
        
            // Add a column filter
            $filter->like('name', 'Name');
            $filter->like('email', 'Email');
        
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
        $show = new Show(User::findOrFail($id));

        $show->id('Id');
        $show->name('Name');
        $show->email('Email');
        $show->type('Type')->as(function ($type) {
            if($type == 1){
                return "Student";
            }else{
                return "Teacher";
            }
        });
        $show->phone('Phone');
        $show->gender('Gender')->as(function ($gender) {
            if($gender == 1){
                return "Male";
            }else{
                return "Female";
            }
        });

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User);
        
        $form->text('name', 'Name');
        $form->text('middleName', 'Middle name');
        $form->text('lastName', 'Last name');
        $form->text('civilIDNumber', 'Civil id number');        
        $form->email('email', 'Email');
        $form->password('password', 'Passwrod');
        $Type= Array(1 => "Stident",2 => "Teacher");
        $form->select("type", "Type")->options($Type)->rules('required');
        $form->image('img', 'Image')->default('images/default.png');
        $form->mobile("phone", "Phone")->rules('required');
        $Gender= Array(1 => "Male",2 => "Female");
        $form->select("gender", "Gender")->options($Gender)->rules('required');
        $form->saving(function (Form $form) {

                if($form->model()->password && $form->password == ""){
                    $form->password = $form->model()->password;
                }
                if($form->password && $form->model()->password != $form->password)
                {
                    $form->password = hash::make($form->password);
                }

            });

        return $form;
    }
}
