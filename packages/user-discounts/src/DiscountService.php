<?php

namespace UserDiscounts;

use UserDiscounts\Models\Discount;
use Illuminate\Support\Facades\DB;

class DiscountService
{
    public function apply(array $discountCodes, $userId, $amount)
    {
        // deterministic stacking: sort codes
        sort($discountCodes);
        $applied = [];
        $totalDiscount = 0.0;

        foreach ($discountCodes as $code) {
            $d = Discount::where('code',$code)->where('active',true)->where(function($q){ $q->whereNull('expires_at')->orWhere('expires_at','>',now()); })->first();
            if (!$d) continue;
            // Simple example: if percentage
            if ($d->type === 'percentage') {
                $disc = ($d->value/100.0) * ($amount - $totalDiscount);
            } else {
                $disc = min($d->value, $amount - $totalDiscount);
            }
            $totalDiscount += $disc;
            $applied[] = ['code'=>$code,'amount'=>$disc];
        }

        // enforce cap
        $cap = config('discounts.max_percentage_cap',50);
        $max = ($cap/100.0) * $amount;
        if ($totalDiscount > $max) $totalDiscount = $max;

        return ['total'=>$totalDiscount,'applied'=>$applied];
    }
}
