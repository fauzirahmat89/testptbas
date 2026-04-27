<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Mail\WelcomeCustomerMail;
use App\Mail\LoyalCustomerMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendStatusEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send {--user_id= : The ID of the customer to send email to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send status-based emails to customers (all or specific)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user_id');

        if ($userId) {
            $customer = Customer::find($userId);
            if (!$customer) {
                $this->error("Customer with ID {$userId} not found.");
                return 1;
            }
            $this->sendEmail($customer);
            $this->info("Email queued for customer: {$customer->name}");
        } else {
            $customers = Customer::all();
            $this->info("Sending emails to " . $customers->count() . " customers...");
            foreach ($customers as $customer) {
                $this->sendEmail($customer);
            }
            $this->info("All emails have been queued.");
        }

        return 0;
    }

    private function sendEmail($customer)
    {
        if ($customer->status === 'NEW CUSTOMER') {
            Mail::to($customer->email)->queue(new WelcomeCustomerMail($customer));
        } elseif ($customer->status === 'LOYAL CUSTOMER') {
            Mail::to($customer->email)->queue(new LoyalCustomerMail($customer));
        }
    }
}
