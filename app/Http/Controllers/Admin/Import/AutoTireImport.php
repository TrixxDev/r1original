<?php

namespace App\Http\Controllers\Admin\Import;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AutoTireImport extends Controller
{
    public function index()
    {
        return true;
    }

    public function import(Request $request)
    {
        return $request->input();
    }
}
