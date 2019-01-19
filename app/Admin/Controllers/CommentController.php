<?php

namespace App\Admin\Controllers;

use App\Comments;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\User;
use App\Lectures;
use App\Courses;

class CommentController extends Controller
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
        $grid = new Grid(new Comments);

        $grid->id('Id');
        $grid->user_id('Username')->display(function($userId) {
            return User::find($userId)->name;
        });
        $grid->lectures_id('Lecture name')->display(function($lectureId) {
            return Lectures::find($lectureId)->title;
        });
        $grid->comment('Comment');
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');

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
        $show = new Show(Comments::findOrFail($id));

        $show->id('Id');
        $show->user_id('User name')->as(function($userId) {
            return User::find($userId)->name;
        });
        $show->lectures_id('Lecture name')->as(function($lectureId) {
            return Lectures::find($lectureId)->title;
        });
        $show->comment('Comment');
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Comments);

        $form->select("user_id", "Owner")->options(User::all()->pluck('name', 'id'));
        $form->select("lecture_id", "Lecture")->options(Lectures::all()->pluck('title', 'id'));
        $form->textarea('comment', 'Comment');

        return $form;
    }
}
