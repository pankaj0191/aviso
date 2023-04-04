<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MahdiMajidzadeh\LaravelUnsplash\Photo;
use MahdiMajidzadeh\LaravelUnsplash\Search;

class UnsplashController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchUnsplash(Request $request)
    {
        $query = $request->search;
        $per_page = $request->per_page ?: 36;
        $orientation = $request->orientation ?: '';
        // $params['page'] = $page;
        $params['per_page'] = $per_page;
        if ($request->has('page')) {
            $params['page'] = $request->page;
        }
        // $params['orientation'] = $orientation;
        // dd('hi');
        $unsplash   = new Search();
        return $unsplash->photo($query, $params)->getArray();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function unsplashDownload($id)
    {
        $unsplash  = new Photo();
        return $unsplash->download($id);
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
