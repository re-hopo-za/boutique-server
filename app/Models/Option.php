<?php

namespace App\Models;

use App\Http\Helper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Option extends Model implements HasMedia
{
    use HasFactory ,InteractsWithMedia;

    public $timestamps = false;

    protected $fillable = [
        'title',
        'key',
        'value',
        'type'
    ];

    public function categories(): MorphToMany
    {
        return $this->morphToMany('App\Models\Category' , 'categorizable' );
    }

    protected function value():Attribute
    {
        return Attribute::make(
            get: fn( $value ) =>  $value ,
            set: fn( $value ) => Helper::clearingHtml( $value ,$this->type )
        );
    }




    public function meta(): MorphMany
    {
        return $this->morphMany('App\Models\Meta', 'metaable');
    }


    /**
     * @throws InvalidManipulation
     */
    public function registerMediaConversions( Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Manipulations::FIT_CROP, 300, 300)
            ->nonQueued();

        $this
            ->addMediaConversion('attachments')
            ->nonQueued()
            ->keepOriginalImageFormat()
            ->withResponsiveImages();
    }



    public function attachment() :mixed
    {
        $images = $this->getMedia( 'attachments' );
        if ($images->count() > 0) {
            return $images[0]->getUrl( 'attachments');
        }
        return '';
    }






}
