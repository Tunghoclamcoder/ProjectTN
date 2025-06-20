<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Voucher extends Model
{
    protected $table = 'vouchers';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'code',
        'discount_amount',
        'discount_percentage',
        'start_date',
        'expiry_date',
        'maximum_purchase_amount',
        'minimum_purchase_amount',
        'max_usage_count',
        'usage_count',
        'status'
    ];

    protected $casts = [
        'start_date' => 'datetime:Y-m-d H:i:s',
        'expiry_date' => 'datetime:Y-m-d H:i:s',
        'status' => 'boolean',
        'minimum_purchase_amount' => 'decimal:2',
        'maximum_purchase_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'usage_count' => 'integer',
        'max_usage_count' => 'integer'
    ];

    public function isValidForUse($total)
    {
        $now = Carbon::now();

        return $this->status &&
            $now->between($this->start_date, $this->expiry_date) &&
            $total >= $this->minimum_purchase_amount &&
            ($this->maximum_purchase_amount === null || $total <= $this->maximum_purchase_amount) &&
            ($this->max_usage_count === null || $this->usage_count < $this->max_usage_count);
    }

    // Tính toán số tiền giảm giá
    public function calculateDiscount($purchaseAmount)
    {
        if ($this->minimum_purchase_amount && $purchaseAmount < $this->minimum_purchase_amount) {
            return 0;
        }

        if ($this->discount_percentage) {
            $discount = $purchaseAmount * ($this->discount_percentage / 100);
        } else {
            $discount = $this->discount_amount;
        }

        if ($this->maximum_purchase_amount) {
            return min($discount, $this->maximum_purchase_amount);
        }

        return $discount;
    }

    // Tăng số lần mỗi khi voucher được sử dụng
    public function incrementUsage()
    {
        $this->usage_count++;
        $this->save();
    }

    // Kiểm tra xem voucher đã được sử dụng bởi khách hàng chưa
    public function hasBeenUsedByCustomer($customerId)
    {
        return Order::where('customer_id', $customerId)
            ->where('voucher_id', $this->id)
            ->exists();
    }
}
