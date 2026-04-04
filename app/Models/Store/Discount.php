<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Actions for this entity can be found in App\Http\Controllers\Admin\Store\DiscountController
 * Class Discount
 * @package App\Models\Store
 */
class Discount extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\DiscountFactory::new();
    }

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'filter',
        'amount',
        'approval',
        'redeemable',
        'value'
    ];

    /**
     * An array of available type options with the key being the database value and the value being the display value in the form
     * @var array
     */
    public $typeArray = array(
        'amount' => 'Dollar Amount',
        'percent' => 'Percentage'
    );

    /**
     * An array of available filter options with the [key] being the database value and the [value] being the display value in the form
     * @var array
     */
    public $filterArray = array(
        'none' => 'No Filter',
        'product' => 'Products Only',
        'liquid' => 'Liquids Only',
    );

    /**
     * Many To Many Relationship with incremental id in the pivot table and applied which is the dollar amount the discount applied
     * @return $this
     */
    public function orders()
    {
        return $this->belongsToMany('App\Models\Store\ShopOrder', 'order_discount')->withPivot('id', 'applied');
    }

}
