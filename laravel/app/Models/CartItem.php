<?php


declare(strict_types=1);


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class CartItem extends Model
{
    use HasFactory;


    protected $fillable = [
        'cart_id',
        'spare_part_id',
        'qty',
        'price_idr',
    ];


    protected $casts = [
        'qty' => 'integer',
        'price_idr' => 'integer',
    ];


    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }


    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class);
    }


    public function getSubtotalAttribute(): int
    {
        return $this->qty * $this->price_idr;
    }
}



