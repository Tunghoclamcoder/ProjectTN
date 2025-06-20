<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'order_id';
    public $timestamps = false;

    protected $fillable = [
        'order_date',
        'order_status',
        'receiver_name',
        'receiver_phone',
        'receiver_address',
        'customer_id',
        'employee_id',
        'voucher_id',
        'payment_method_id',
        'shipping_method_id'
    ];

    protected $casts = [
        'order_date' => 'date',
    ];

    // Relationship with Customer
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    // Relationship with Employee
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    // Relationship with Voucher
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class, 'voucher_id', 'id');
    }

    // Relationship with PaymentMethod
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id', 'method_id');
    }

    // Relationship with ShippingMethod
    public function shipping_method(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id', 'method_id');
    }

    // For admin panel (camelCase)
    public function shippingMethod(): BelongsTo
    {
        return $this->shipping_method();
    }

    // Relationship with OrderDetails
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'order_id');
    }

    // Helper method to get status label
    // Add constants for order statuses
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_SHIPPING = 'shipping';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_RETURNED = 'returned';

    // Update getStatusLabel to use constants
    public function getStatusLabel()
    {
        $statuses = [
            self::STATUS_PENDING => 'Chờ xác nhận',
            self::STATUS_CONFIRMED => 'Đã xác nhận',
            self::STATUS_SHIPPING => 'Đang giao hàng',
            self::STATUS_COMPLETED => 'Đã hoàn thành',
            self::STATUS_CANCELLED => 'Đã hủy',
            self::STATUS_RETURNED => 'Đã hoàn trả'
        ];

        return $statuses[$this->order_status] ?? $this->order_status;
    }

    // Scope for filtering orders by status
    public function scopeStatus($query, $status)
    {
        return $query->where('order_status', $status);
    }

    // Scope for filtering orders by date range
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('order_date', [$startDate, $endDate]);
    }

    public function getTotalAmount()
    {
        // Calculate base total
        $total = $this->orderDetails->sum(function ($detail) {
            return $detail->sold_price * $detail->sold_quantity;
        });

        // Use shipping_method() to support both naming conventions
        if ($this->shipping_method) {
            $total += $this->shipping_method->shipping_fee;
        }

        // Subtract voucher discount if exists
        if ($this->voucher) {
            $total -= $this->getDiscountAmount();
        }

        return $total;
    }

    public function getDiscountAmount()
    {
        if (!$this->voucher) {
            return 0;
        }

        $total = $this->orderDetails->sum(function ($detail) {
            return $detail->sold_price * $detail->sold_quantity;
        });

        return $this->voucher->discount_percentage
            ? ($total * $this->voucher->discount_percentage / 100)
            : $this->voucher->discount_amount;
    }

    public function getFinalTotal()
    {
        // Calculate products total
        $total = $this->orderDetails->sum(function ($detail) {
            return $detail->sold_price * $detail->sold_quantity;
        });

        // Add shipping fee if exists
        if ($this->shipping_method) {
            $total += $this->shipping_method->shipping_fee;
        }

        // Subtract discount amount if voucher exists
        $total -= $this->getDiscountAmount();

        return $total;
    }

    public function isReturned(): bool
    {
        return $this->order_status === 'returned';
    }


    // Add method to check if order can be returned
    public function canBeReturned(): bool
    {
        return $this->order_status === self::STATUS_COMPLETED;
    }

    // Add method to process order return
    public function processReturn()
    {
        if (!$this->canBeReturned()) {
            throw new \Exception('Đơn hàng không thể hoàn trả.');
        }

        DB::transaction(function () {
            // Update order status
            $this->update(['order_status' => 'returned']);

            // Return products to inventory
            foreach ($this->orderDetails as $detail) {
                $detail->product->increment('quantity', $detail->sold_quantity);
            }
        });

        return true;
    }


    // Optional: Add a scope for returned orders
    public function scopeReturned($query)
    {
        return $query->where('order_status', 'returned');
    }
}
