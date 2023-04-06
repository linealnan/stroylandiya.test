<?php 
declare(strict_types=1);

namespace GeoIp;

use Bitrix\Main\Diag\Debug;

class GeoIPDto
{
    public $ip; // string
    public $type; // "ipv4"
    public $continent_code; // "AS"
    public $continent_name; // "Asia"
    public $country_code; // "RU"
    public $country_name; // "Russia"
    public $region_code; // "ORE"
    public $region_name; // "Orenburg Oblast"
    public $city; // "Orenburg"
    public $zip; // "460961"
    public $latitude; //51.784698486328125, 
    public $longitude; // 55.09859848022461,
    // public $continent_code; 
    // public $location; 
    //  private location": {"geoname_id": 515003, "capital": "Moscow", "languages": [{"code": "ru", "name": "Russian", "native": "\u0420\u0443\u0441\u0441\u043a\u0438\u0439"}], "country_flag": "https://assets.ipstack.com/flags/ru.svg", "country_flag_emoji": "\ud83c\uddf7\ud83c\uddfa", "country_flag_emoji_unicode": "U+1F1F7 U+1F1FA", "calling_code": "7", "is_eu": false}}"

    public function __construct() {

    }
}