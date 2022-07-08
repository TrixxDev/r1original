<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RimsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $rims = Rim::selectRaw('rims.*, rim_brands.brand_id as brand_id, rim_brands.title as brand_title, rim_makes.make_id as make_id, rim_makes.title as make_title')
                   ->leftJoin('rim_makes', 'rim_makes.make_id', '=', 'rims.make_id')
                   ->leftJoin('rim_brands', 'rim_makes.brand_id', '=', 'rim_brands.brand_id')
                   ->orderBy('d3', 'asc')
                   ->orderBy('d1', 'asc')
                   ->orderBy('price2', 'asc')
                   ->orderBy('rim_makes.title', 'asc')
                   ->get();

        $current_r = -10;

        return view('admin.rims.index', compact('current_r', 'rims'));
    }

    /**a
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
