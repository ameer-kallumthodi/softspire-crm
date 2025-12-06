<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\CountryRequest;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends BaseController
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getData($request);
        }
        return view('admin.countries.index');
    }

    private function getData(Request $request)
    {
        $where = [];
        
        if ($request->has('search') && $request->search != '') {
            $where['name'] = ['like', "%{$request->search}%"];
        }

        if ($request->has('status') && $request->status != '') {
            $where['status'] = $request->status;
        }

        $countries = Country::get($where, null, ['id', 'desc'], null, null);

        return $this->jsonSuccess('Countries retrieved', $countries);
    }

    public function create()
    {
        return view('admin.countries.create');
    }

    public function ajaxAdd()
    {
        return view('admin.countries.ajax_add');
    }

    public function ajaxEdit(Country $country)
    {
        return view('admin.countries.ajax_edit', compact('country'));
    }

    public function store(CountryRequest $request)
    {
        $country = Country::create($request->validated());

        if ($request->ajax()) {
            return $this->jsonSuccess('Country created successfully', $country);
        }

        return redirect()->route('admin.countries.index')->with('success', 'Country created successfully.');
    }

    public function show(Country $country)
    {
        return view('admin.countries.show', compact('country'));
    }

    public function edit(Country $country)
    {
        return view('admin.countries.edit', compact('country'));
    }

    public function update(CountryRequest $request, Country $country)
    {
        $country->update($request->validated());

        if ($request->ajax()) {
            return $this->jsonSuccess('Country updated successfully', $country);
        }

        return redirect()->route('admin.countries.index')->with('success', 'Country updated successfully.');
    }

    public function destroy(Country $country)
    {
        $country->delete();

        if (request()->ajax()) {
            return $this->jsonSuccess('Country deleted successfully');
        }

        return redirect()->route('admin.countries.index')->with('success', 'Country deleted successfully.');
    }

    public function toggleStatus(Country $country)
    {
        $country->status = $country->status === 'active' ? 'inactive' : 'active';
        $country->save();

        if (request()->ajax()) {
            return $this->jsonSuccess('Status updated successfully', $country);
        }

        return redirect()->back()->with('success', 'Status updated successfully.');
    }
}

