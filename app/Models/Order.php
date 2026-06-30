<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
    'product_id',
    'user_id',
    'buyer_id',
    'jumlah',
    'total_harga',
    'review_status',
    'rejection_reason',
    'status',
    'shipping_method',
    'delivery_location',
    'payment_method',
    'payment_status',
    'payment_proof',
    'rating',
    'review_text',
    'review_image',
    'reviewed_at',
];

    protected $casts = [
        'rating' => 'integer',
        'reviewed_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function scopeCompletedReviews($query)
    {
        return $query->where('status', 'selesai')->whereNotNull('rating');
    }
}