<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Mail\WelcomeCustomerMail;
use App\Mail\LoyalCustomerMail;
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
            ->addColumn('action', function ($customer) {
                if ($customer->status == 'NEW CUSTOMER') {
                    return '<button class="btn btn-sm btn-soft-success" onclick="makeLoyal(\'' . $customer->user_id . '\')">
                                <i class="ri-user-star-line align-bottom me-1"></i> Make Loyal
                            </button>';
                }
                return '<button class="btn btn-sm btn-light disabled">Already Loyal</button>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function changeStatus(Request $request, $id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->status = 'LOYAL CUSTOMER';
            $customer->save();

            // Kirim email Loyal menggunakan Queue
            Mail::to($customer->email)->queue(new LoyalCustomerMail($customer));

            return response()->json([
                'success' => true,
                'message' => 'Customer status updated to LOYAL and email sent.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
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
