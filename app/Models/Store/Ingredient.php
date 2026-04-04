<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Actions for this entity can be found in App\Http\Controllers\Admin\Store\IngredientController
 * Class Ingredient
 * @package App\Models\Store
 */
class Ingredient extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\IngredientFactory::new();
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
        'vendor'
    ];

    /**
     * Many to Many with pivot being the amount of the ingredient in the recipe
     * @return $this
     */
    public function recipes() {
        return $this->belongsToMany('App\Models\Store\Recipe', 'recipe_ingredient')->withPivot('amount');
    }
}
