<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function userlocations(User $user)
    {
        return [
            'list' => $user->locations,
            'placeIds' => $user->getPlaceIds(),
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('location.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->all();
        $validated['place_id'] = isset($validated['place_id']) ? $validated['place_id'] : $validated['id'];
        return $request->user()->locations()->create($validated);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function show(Location $location)
    {
        return $location;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $location)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Location $location)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function destroy(Location $location)
    {
        return $location->delete();
    }

    public function destroyByPlaceId(User $user, $placeId)
    {
        $location = $this->getUserLocationByPlaceId($user, $placeId);
        return $location->delete();
    }

    public function getUserLocationByPlaceId(User $user, $placeId)
    {
        $result = $user->locations()->where('place_id', $placeId)->orderBy('id', 'desc')->first();
        return $result;
    }

    public function getUserPlaceId(User $user)
    {
        return $user->getPlaceIds();
    }

    public function editMemo(Request $request)
    {
        $validated = $request->all();
        $validated['place_id'] = isset($validated['place_id']) ? $validated['place_id'] : $validated['id'];

        $location = $request->user()->locations()->where('place_id', $validated['place_id'])->first();

        if($location){
            $result = $location->update($validated);
        } else {
            $result = $request->user()->locations()->create($validated);
        }

        return $result;
    }
}
