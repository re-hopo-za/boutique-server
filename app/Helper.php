<?php


namespace App\Http;


use App\Models\Option;
use Illuminate\Support\Facades\Cache;
use stdClass;

class Helper
{


    public static function clearingHtml( $content ,$type ) :string
    {
        if ( $type == 0 ){
            return html_entity_decode( strip_tags( $content ) );
        }
        return $content;
    }


    public static function indexChecker($data, $index, $default = '') :mixed
    {
        if (!empty($data)) {
            if (is_object($data) && isset($data->$index)) {
                return $data->$index;
            }elseif (is_array($data) && isset($data[$index])) {
                return $data[$index];
            }
        }
        return $default;
    }


    public static function formIndexChecker( $data ,$index ,$default ='' ):string
    {
        if (!empty( $data ) ) {
            if ( is_object( $data ) && isset( $data->$index ) ){
                return str_replace(PHP_EOL, '', self::clearingHtml( $data->$index ,0 ) );
            }elseif ( is_array( $data ) && isset( $data[$index] ) ){
                return self::clearingHtml( $data[$index] ,0 );
            }
        }
        return $default;
    }



    public static function returnValueIsTrue($data, $index, $value, $default = '')
    {
        if (!empty( self::indexChecker($data, $index))) {
            return $value;
        }
        return $default;
    }

    public static function checkCurrentPage($page): string
    {
        if ((request()->route()->getName() == $page) || ($page != '/' && str_contains(request()->url, $page))) {
            return 'current-menu-item';
        }
        return '';
    }

    public static function breadcrumbsRender( $route ): string
    {
        $breadcrumb = '';
        if (!empty( $route ) && is_array( $route ) ) {
            foreach ( $route as $name => $link ) {
                $breadcrumb .= '<span class="sep">|</span>';
                if (!empty($link)) {
                    $breadcrumb .= "<a class='trail-begin' href='{$link}'> {$name} </a>";
                } else {
                    $breadcrumb .= '<span class="trail-end">' . self::getPageTranslatedTitle( $name ) . '</span>';
                }
            }
        }
        return $breadcrumb;
    }


    public static function getPageTranslatedTitle($page): string
    {
        switch (strtolower(trim($page))) {
            case 'home':
                return 'خانه';
            case 'product':
                return 'محصولات';
            case 'project':
                return 'پروژ‌ها';
            case 'service':
                return 'خدمات';
            case 'blog':
                return 'مقاله‌ها';
            case 'resource':
                return 'منابع';
            case 'contact':
                return 'تماس با ما';
            case 'about':
                return 'درباره‌ما';
            case 'Category Resource':
                return 'دسته‌بندی منابع';
            default:
                return $page;
        }
    }


    public static function slugRectifier($string)
    {
        if (!empty($string)) {
            $string = trim($string);
            $string = mb_strtolower($string, "UTF-8");
            $string = preg_replace("/[^a-z0-9_\s\-ءاأإآؤئبتثجحخدذرزسشصضطظعغفقكلمنهويپگچةى]#u/", "", $string);
            $string = preg_replace("/[\s-]+/", " ", $string);
            $string = preg_replace("/[\s_]/", '-', $string);
        }
        return $string;
    }


    public static function fileIcon($filename)
    {
        if (!empty($filename)) {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            switch ($ext) {
                case 'pdf':
                    return '<i class="fa fa-file-pdf-o"></i>';
                case 'docx':
                case 'doc':
                case 'txt':
                    return '<i class="fa fa-file-word-o"></i>';
                case 'xlsx':
                case 'xls':
                    return '<i class="fa fa-file-excel-o"></i>';
                case 'pptx':
                case 'ppt':
                    return '<i class="fa fa-file-powerpoint-o"></i>';
                case 'rar':
                case 'zip':
                    return '<i class="fa fa-file-archive-o"></i>';
                case 'png':
                case 'gif':
                case 'jpg':
                    return '<i class="fa fa-file-image-o"></i>';
                case 'mp3':
                case 'wma':
                case 'wav':
                    return '<i class="fa fa-file-audio-o"></i>';
                case 'mp4':
                    return '<i class="fa fa-file-video-o"></i>';
            }
        }
        return '';
    }


    public static function randomColor(): string
    {
        return array_rand(array_flip(['16183F', '474554', 'ACA9BB', '003B2A', '1F6B58', '4A2A00', '005E8E']));
    }


    public static function sliderPosition($position): string
    {
        return match ($position) {
            'right' => "['right','right','right','center']",
            'center' => "['center','center','center','center']",
            'left' => "['left','left','left','center']",
        };
    }

    public static function information():object
    {
        return Cache::rememberForever( 'information' ,function (){
            $details = Option::where('key' ,'information')->with('meta')->first();
            $object  = new stdClass();
            if ( !empty( $details ) ){
                $object->description = $details?->value;
                $object->logo        = $details?->attachment();
                if ( $details->meta->isNotEmpty() ){
                    foreach ( $details->meta()->pluck('value' ,'key')->toArray() as $key => $val ){
                        $object->{$key} = $val;
                    }
                }
            }
            return $object;
        });
    }



}
