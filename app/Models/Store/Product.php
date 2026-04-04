<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Actions for this entity can be found in App\Http\Controllers\Admin\Store\ProductController
 * Class Product
 * @package App\Models\Store
 */
class Product extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\ProductFactory::new();
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
        'sku',
        'category',
        'cost'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * One to Many
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productInstances() {
        return $this->hasMany('App\Models\Store\ProductInstance');
    }
}
