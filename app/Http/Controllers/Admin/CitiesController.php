<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CitiesController extends Controller
{
    public function index()
    {
        $cities = City::all();

        return view('admin.cities.index', compact('cities'));
    }

    public function create()
    {
        return view('admin.cities.create');
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:cities'],
            'region' => ['nullable', 'string', 'max:255'],
        ]);

        // Create the city
        City::create([
            'name' => $request->name,
            'region' => $request->region,
        ]);

        return redirect()->route('admin.cities.index')->with('message', 'City created successfully.');
    }

    public function edit(City $city)
    {
        return view('admin.cities.edit', compact('city'));
    }

    public function update(Request $request, City $city)
    {
        // Validate the request
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:cities,name,' . $city->id],
            'region' => ['nullable', 'string', 'max:255'],
        ]);

        // Update the city
        $city->update([
            'name' => $request->name,
            'region' => $request->region,
        ]);

        return redirect()->route('admin.cities.index')->with('message', 'City updated successfully.');
    }

    public function show(City $city)
    {
        return view('admin.cities.show', compact('city'));
    }

    public function destroy(City $city)
    {
        $city->delete();

        return back()->with('message', 'City deleted successfully.');
    }
}