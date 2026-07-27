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
                // This event only fires once per payment row transitioning to
                // confirmed, so each row's own (instance_code, app_id) privilege
                // is extended exactly once regardless of how many other rows
                // share the same transaction_id.
                $privs = InstancePriviledges::where('instance_code', $payment->instance_code)
                    ->where('app_id', $payment->app_id)
                    ->get();

                foreach ($privs as $p) {
                    // Extend from the current expiry only if the privilege is
                    // still active; if it already lapsed, the new 30-day
                    // period starts from now instead of compounding onto a
                    // stale past date.
                    $base = $p->expired_at && Carbon::parse($p->expired_at)->isFuture()
                        ? Carbon::parse($p->expired_at)
                        : now();

                    $p->update([
                        'expired_at' => $base->addDays(30),
                    ]);
                }
            }
        });
    }

}
