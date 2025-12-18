<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Customer;
use App\Models\Country;
use Illuminate\Http\Request;

class CustomerController extends BaseController
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getData($request);
        }
        
        $countries = Country::get(['status' => 'active'], null, null, null, null);
        
        return view('admin.customers.index', compact('countries'));
    }

    private function getData(Request $request)
    {
        $where = [];
        $whereOR = [];

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $whereOR['name'] = ['like', "%{$search}%"];
            $whereOR['phone'] = ['like', "%{$search}%"];
            $whereOR['email'] = ['like', "%{$search}%"];
            $where['OR'] = $whereOR;
        }

        if ($request->has('country_id') && $request->country_id != '') {
            $where['country_id'] = $request->country_id;
        }

        $customers = Customer::buildQuery($where, null, ['id', 'desc'], null)
            ->with(['country', 'purpose', 'telecaller', 'lead', 'convertedBy'])
            ->get();

        return $this->jsonSuccess('Customers retrieved', $customers);
    }

    public function show(Customer $customer)
    {
        $customer->load(['country', 'purpose', 'telecaller', 'lead', 'convertedBy']);
        
        // Load lead activities if lead exists
        if ($customer->lead) {
            $customer->lead->load(['activities.leadStatus']);
        }
        
        if (request()->ajax()) {
            return $this->jsonSuccess('Customer retrieved', $customer);
        }
        
        return view('admin.customers.show', compact('customer'));
    }
}
