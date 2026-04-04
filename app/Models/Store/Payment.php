<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Actions for this entity can be found in App\Http\Controllers\Front\Store\ShopOrderController
 * Class Payment
 * @package App\Models\Store
 */
class Payment extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\PaymentFactory::new();
    }

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var array
     */
    protected $fillable = [
        'shop_order_id',
        'type',
        'amount'
    ];

    /**
     * Many to One
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Store\ShopOrder');
    }
}
