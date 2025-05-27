<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function icons()
    {
        return view('pages.icons');
    }

    public function maps()
    {
        return view('pages.maps');
    }

    public function tables()
    {
        return view('pages.tables');
    }

    public function notifications()
    {
        return view('pages.notifications');
    }

    public function typography()
    {
        return view('pages.typography');
    }
}
