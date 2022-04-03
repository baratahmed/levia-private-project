<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];

    public static function getValue($key, $default = null){
        $setting = static::where('key', $key)->first();

        if ($setting){
            $type = gettype($default);

            $value = $setting->value;

            if ( "double" ===$type ){
                $value = (double) doubleval($value);
            }
            else if ( "string" ===$type ){
                $value = (string) $value;
            }
            else if ( "integer" ===$type ){
                $value = (int) intval($value);
            }

            return $value;
        }

        

        return $default;
    }

    public static function setValue($key, $value = null, $create_mode = false){
        $setting = static::where('key', $key)->first();

        if ( null !== $setting ){
            $setting->value = $value;
            $setting->save();
        } else if ($create_mode){
            $setting = new static([
                'key' => $key,
                'value' => $value
            ]);
            $setting->save();
        }
        

        return $setting;
    }
}
