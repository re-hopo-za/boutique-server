<?php

namespace App\Models;

use App\Http\Helper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'cover',
        'parent_id'
    ];

    protected function slug():Attribute
    {
        return Attribute::make(
            get: fn( $value ) =>  $value ,
            set: fn( $value ) => Helper::slugRectifier( $value )
        );
    }

    public function meta(): MorphMany
    {
        return $this->morphMany('App\Models\Meta', 'metaable');
    }

    public function searchCategory( $search ,$model = '')
    {
        $where = [ ['title' ,'like' ,"%{$search}%"]];
        if ( !empty( $model ) ){
            $where[] = ['model' ,'=' ,$model];
        }
        return $this
            ->where( $where )
            ->pluck('slug' ,'slug' );
    }

}
