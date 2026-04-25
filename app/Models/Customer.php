<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Customer extends Model
{
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'status',
    ];

    protected static function booted()
    {
        static::creating(function ($customer) {
            if (empty($customer->user_id)) {
                $customer->user_id = static::generateUserId();
            }
        });
    }

    public static function generateUserId()
    {
        $date = Carbon::now()->format('dmY');
        $lastCustomer = static::where('user_id', 'like', $date . '%')
            ->orderBy('user_id', 'desc')
            ->first();

        if ($lastCustomer) {
            $lastIncrement = (int) substr($lastCustomer->user_id, 8);
            $newIncrement = str_pad($lastIncrement + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newIncrement = '001';
        }

        return $date . $newIncrement;
    }
}
