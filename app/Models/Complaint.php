<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $appends = [
        'nama_toko',
        'uraian_keluhan',
        'masukan',
    ];

    protected $fillable = [
        'user_id',
        'order_id',
        'complaint_type',
        'forwarded_from_id',
        'admin_reply',
        'sender_name',
        'issue_description',
        'input',
        'subject',
        'message',
        'reported_user_id',
        'reported_product_id',
        'bukti',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    public function reportedProduct()
    {
        return $this->belongsTo(Product::class, 'reported_product_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function forwardedFrom()
    {
        return $this->belongsTo(self::class, 'forwarded_from_id');
    }

    // Backward-compatible aliases used by existing controllers/views.
    public function getNamaTokoAttribute()
    {
        return $this->attributes['nama_toko'] ?? $this->attributes['sender_name'] ?? null;
    }

    public function setNamaTokoAttribute($value)
    {
        $this->attributes['nama_toko'] = $value;
        $this->attributes['sender_name'] = $value;
    }

    public function getUraianKeluhanAttribute()
    {
        return $this->attributes['uraian_keluhan'] ?? $this->attributes['issue_description'] ?? null;
    }

    public function setUraianKeluhanAttribute($value)
    {
        $this->attributes['uraian_keluhan'] = $value;
        $this->attributes['issue_description'] = $value;
    }

    public function getMasukanAttribute()
    {
        return $this->attributes['masukan'] ?? $this->attributes['input'] ?? null;
    }

    public function setMasukanAttribute($value)
    {
        $this->attributes['masukan'] = $value;
        $this->attributes['input'] = $value;
    }
}
