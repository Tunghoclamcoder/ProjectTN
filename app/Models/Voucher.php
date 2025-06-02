<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'start_date' => 'date',
        'expiry_date' => 'date',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'maximum_purchase_amount' => 'decimal:0',
        'minimum_purchase_amount' => 'decimal:0',
        'status' => 'boolean'
    ];

    // Kiểm tra voucher có còn hiệu lực không
    public function isValid()
    {
        $now = now();
        return $this->status
            && $now->between($this->start_date, $this->expiry_date)
            && ($this->max_usage_count === null || $this->usage_count < $this->max_usage_count);
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
