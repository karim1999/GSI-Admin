<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use App\User;
use App\Lectures;
use App\Comments;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Tab ;

// $items = [
//     'header'      => $this->header,
//     'description' => $this->description,
//     'breadcrumb'  => $this->breadcrumb,
//     'content'     => $this->build(),
// ];

// return view('admin::content', $items)->render();

class HomeController extends Controller
{
    public function index(Content $content)
    {
        // $totals = [
        //    'users' => User::all()->count(),
        //     'lectures' => Lectures::all()->count(),
        // ];
        // return view('dashboard', compact('totals'));
        
        return $content
            // ->header('Dashboard')
            // ->description('Description...')
            // ->row(Dashboard::title())
            ->row(function (Row $row) {

                $row->column(3, function (Column $column) {
                    $column->append(
                        $box = new Box('Total students', User::where('type', 1)->count()),
                        
                        $box->removable(),
                        
                        $box->collapsable(),
                        
                        $box->style('primary'),
                        
                        $box->solid()
                    );
                });

                $row->column(3, function (Column $column) {
                    $column->append(
                        $box = new Box('Total teachers', User::where('type', 2)->count()),
                        
                        $box->removable(),
                        
                        $box->collapsable(),
                        
                        $box->style('primary'),
                        
                        $box->solid()
                    );
                });

                $row->column(3, function (Column $column) {
                    $column->append(
                        $box = new Box('Total leactures', Lectures::all()->count()),
                        
                        $box->removable(),
                        
                        $box->collapsable(),
                        
                        $box->style('primary'),
                        
                        $box->solid()
                    );
                });

                $row->column(3, function (Column $column) {
                    $column->append(
                        $box = new Box('Total comments', Comments::all()->count()),
                        
                        $box->removable(),
                        
                        $box->collapsable(),
                        
                        $box->style('primary'),
                        
                        $box->solid()
                    );
                });

            });
     }
}
