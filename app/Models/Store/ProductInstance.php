<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * actions for this entity can be found in...
 * App\Http\Controllers\Admin\Store\ProductController
 * App\Http\Controllers\Admin\Store\InventoryController
 *
 * Class ProductInstance
 * @package App\Models\Store
 */
class ProductInstance extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\ProductInstanceFactory::new();
    }

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var array
     */
    protected $fillable = [
        'price',
        'stock',
        'redline',
        'active',
        'store',
        'product_id'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * Many to One with App\Models\Store\Product
     */
    public function product() {
        return $this->belongsTo('App\Models\Store\Product');
    }

    /**
     * Many to Many with App\Models\Store\ShopOrder
     */
    public function orderProduct() {
        return $this->belongsToMany('App\Models\Store\ShopOrder', 'order_product')->withPivot('quantity', 'id');
    }

    /**
     * Many to Many with App\Models\Store\Shipment
     */
    public function productShipment() {
        return $this->belongsToMany('App\Models\Store\Shipment', 'instance_shipment')->withPivot('quantity', 'id');
    }

    /**
     * Many to Many with App\Models\Store\Transfer
     */
    public function productTransfer() {
        return $this->belongsToMany('App\Models\Store\Transfer', 'instance_transfer')->withPivot('quantity', 'id', 'received');
    }
}
