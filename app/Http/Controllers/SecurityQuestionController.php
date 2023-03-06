<?php

namespace App\Http\Controllers;

use App\Models\SecurityQuestion;
use Illuminate\Http\Request;

class SecurityQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SecurityQuestion::all();
    }

    /**
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
     * @param  \App\Models\SecurityQuestion  $securityQuestion
     * @return \Illuminate\Http\Response
     */
    public function show(SecurityQuestion $securityQuestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SecurityQuestion  $securityQuestion
     * @return \Illuminate\Http\Response
     */
    public function edit(SecurityQuestion $securityQuestion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SecurityQuestion  $securityQuestion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SecurityQuestion $securityQuestion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SecurityQuestion  $securityQuestion
     * @return \Illuminate\Http\Response
     */
    public function destroy(SecurityQuestion $securityQuestion)
    {
        //
    }
}
