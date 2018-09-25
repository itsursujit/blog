<?php

namespace Modules\Gateway\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Gateway\Http\Requests\Request;

class ApiGatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return 'API';
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('gateway::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     *
     * @return void
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('gateway::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('gateway::edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     *
     * @return void
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return void
     */
    public function destroy()
    {
    }
}
