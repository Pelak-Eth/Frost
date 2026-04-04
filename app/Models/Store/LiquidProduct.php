<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Actions for this entity can be found in App\Http\Controllers\Front\Store\ShopOrderController
 * Class LiquidProduct
 * @package App\Models\Store
 */
class LiquidProduct extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\LiquidProductFactory::new();
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
        'recipe_id',
        'store',
        'size',
        'nicotine',
        'vg',
        'menthol',
        'extra',
        'mixed',
        'salt'
    ];

    /**
     * Many to One
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipe() {
        return $this->belongsTo('App\Models\Store\Recipe');
    }

    /**
     * Many to One
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shopOrder() {
        return $this->belongsTo('App\Models\Store\ShopOrder');
    }

    /**
     * Returns the price of the LiquidProduct based on config settings and if Extra is true add $1
     * @return float $price
     */
    public function getPrice() {
        $price = config('store.bottle_prices')[$this->size];
        if($this->extra) {
            $price += 1;
        }
        if ($this->salt) {
            $price += 1;
        }
        if ($this->nicotine >= 40) {
            $price += 1;
        }
        return $price;
    }
}
