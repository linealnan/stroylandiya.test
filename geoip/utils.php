<?php
namespace GeoIp;

use Bitrix\Main\Diag\Debug;

class Utils
{
    public static function mapJsonToObj($json, $Obj)
    {
        $dcod = json_decode($json);
        $prop = get_object_vars($dcod);

        foreach($prop as $key => $lock)
        {
            if(property_exists($Obj, $key))
            {
                if(is_object($dcod->$key))
                {
                    self::mapJsonToObj(json_encode($dcod->$key), $Obj->$key);
                } else {
                    $Obj->$key = $dcod->$key;
                }
            } else {
                // Добавить исключение
                Debug::dump($key);
            }
        }
        return $Obj;
    }
}