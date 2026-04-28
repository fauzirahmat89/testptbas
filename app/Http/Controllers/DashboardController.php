<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $newCustomerCount = Customer::where('status', 'NEW CUSTOMER')->count();
        $loyalCustomerCount = Customer::where('status', 'LOYAL CUSTOMER')->count();

        return view('dashboard', compact('newCustomerCount', 'loyalCustomerCount'));
    }
}
