<?php

namespace App\Models\Fianut;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Models\Fianut\InstancePriviledges;

class AppPayments extends Model
{
     // use HasFactory;
     protected $table = 'app_payments';
     // pk
     public $primaryKey = 'id';

     public $timestamps = true;

     protected $guarded = ['id'];

    public function appPricing()
    {
         return $this->belongsTo(AppPricings::class, 'app_pricings_id');
    }

    /**
     * When a payment is updated and its confirm_payment changes from not-confirmed to confirmed,
     * extend the corresponding InstancePriviledges.expired_at by 30 days.
     */
    protected static function booted()
    {
        static::updated(function ($payment) {
            $original = $payment->getOriginal('confirm_payment');
            $current = $payment->confirm_payment;

            if (($original != 1) && ($current == 1)) {
                // Only extend privileges once per transaction_id. When multiple
                // AppPayments share the same transaction, AdminController will
                // update them in a DB transaction; the first confirmed payment
                // should trigger the extension, subsequent ones should not.
                $confirmedCount = self::where('transaction_id', $payment->transaction_id)
                    ->where('confirm_payment', 1)
                    ->count();

                if ($confirmedCount === 1) {
                    $privs = InstancePriviledges::where('instance_code', $payment->instance_code)
                        ->where('app_id', $payment->app_id)
                        ->get();

                    foreach ($privs as $p) {
                        $p->update([
                            'expired_at' => Carbon::parse($p->expired_at ?? now())->addDays(30),
                        ]);
                    }
                }
            }
        });
    }

}
