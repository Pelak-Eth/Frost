<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\ShiftFactory::new();
    }

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var array
     */
    protected $fillable = [
        'store',
        'start',
        'end',
        'in',
        'out',
        'user_id'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\Models\Auth\User')->withTrashed();
    }
}
