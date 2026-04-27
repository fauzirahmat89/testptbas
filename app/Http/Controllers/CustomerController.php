<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Mail\WelcomeCustomerMail;
use Illuminate\Support\Facades\Mail;

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
            ->editColumn('created_at', function ($customer) {
                return $customer->created_at ? $customer->created_at->format('d M, Y H:i') : '-';
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

            // Kirim email menggunakan Queue
            Mail::to($customer->email)->queue(new WelcomeCustomerMail($customer));

            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully and welcome email queued.',
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
