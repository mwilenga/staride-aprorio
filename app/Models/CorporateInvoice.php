<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateInvoice extends Model
{
    use HasFactory;

    public function Corporate()
    {
        return $this->belongsTo(Corporate::class);
    }

    public function details()
    {
        return $this->hasMany(CorporateInvoiceDetail::class);
    }
    public function invoicePartialSettlement()
    {
        return $this->hasMany(CorporatePartialSettlement::class);
    }
    public function latestPartialSettlement()
    {
        return $this->hasOne(CorporatePartialSettlement::class)->latestOfMany();
    }
}
