<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function index()
    {
        return view('customers.index');
    }

    public function data()
    {
        $customers = Customer::query();

        return DataTables::of($customers)
            ->editColumn('status', function ($customer) {
                $class = $customer->status == 'LOYAL CUSTOMER' ? 'bg-success' : 'bg-info';
                return '<span class="badge ' . $class . '">' . $customer->status . '</span>';
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255'
        ]);

        try {
            $validated['status'] = 'NEW CUSTOMER';
            $customer = Customer::create($validated);
            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create customer: ' . $e->getMessage()
            ], 500);
        }
    }
}
