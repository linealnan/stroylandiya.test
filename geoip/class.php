<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
include 'utils.php';
include 'geo_ip_dto.php';

use Bitrix\Main;
use Bitrix\Main\Loader; 
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Diag\Debug;
use GeoIp\GeoIPDto;
use Bitrix\Main\UI\Extension;

Loader::includeModule("highloadblock");
CJSCore::Init(array("ajax"));

use Bitrix\Highloadblock as HL; 
use Bitrix\Main\Entity;

Loc::loadMessages(__FILE__);


class GeoIP extends \CBitrixComponent
{
    /** @var Main\Context $context */
    protected $app = null;
    protected $httpClient;
    protected $hlEntity;
    protected $geoipInfo;

    public function __construct($component = null)
    {
        global $APPLICATION;

        $this->app = $APPLICATION;
        parent::__construct($component);
        
        $this->httpClient = $this->initHttpClient();
    }

    public function executeComponent()
    {
        $request = Main\Application::getInstance()->getContext()->getRequest();
        $this->checkAndHandleComponentParams();
        $this->hlEntity = $this->getHighloadBlockEntity($this->arParams["HLBL_ID"]);
        
        if (!$request->isAjaxRequest()) {
            $this->app->ShowHead();
            Extension::load("ui.vue");
            if($this->startResultCache())
            {
                $this->includeComponentTemplate();
            }
        } else {
            $this->processAJAX($request);
        }
        return $this->arResult;
    }

    /**
     * AJAX режим работы компонента
     * 
     * @todo вынесети преобразование данных GeoIPDto из инфболока в маппер
     */
    private function processAJAX($request) {
        // Debug::dump($request->get('ip'));
        $searchIp = $request->get('ip');
        // $searchIp = '95.105.124.118';
        
        if (!$this->isIp($searchIp)) {
            $this->showAjaxAnswer(['Error' => Loc::getMessage('IP_ADDRESS_FORMAT_ERROR')], $responseCode = 400);
        }

        $localSearhingResult = $this->findIpDataForHighloadBlock($searchIp);
        
        if (!$localSearhingResult) {
            $result = \GeoIp\Utils::mapJsonToObj($this->getIp($searchIp), new GeoIPDto());
            $this->addIpInfo($result);
            $this->geoipInfo = $result;
        } else {
            
            $GeoIPDto = new GeoIPDto();
            $GeoIPDto->ip = $localSearhingResult['UF_IP'];
            $GeoIPDto->type  = $localSearhingResult['UF_TYPE'];
            $GeoIPDto->continent_code = $localSearhingResult['UF_CONTINENT_CODE'];
            $GeoIPDto->continent_name = $localSearhingResult['UF_CONTINENT_NAME'];
            $GeoIPDto->country_code = $localSearhingResult['UF_COUNRTRY_CODE'];
            $GeoIPDto->country_name = $localSearhingResult['UF_COUNRTRY_NAME'];
            $GeoIPDto->region_code = $localSearhingResult['UF_REGION_CODE'];
            $GeoIPDto->region_name = $localSearhingResult['UF_REGION_NAME'];
            $GeoIPDto->city = $localSearhingResult ['UF_CITY'];
            $GeoIPDto->zip = $localSearhingResult['UF_ZIP"'];
            $GeoIPDto->zip = $localSearhingResult['UF_ZIP'];
            $GeoIPDto->latitude = $localSearhingResult['UF_LATITUDE'];
            $GeoIPDto->longitude = $localSearhingResult['UF_LONGITUDE'];
            
            $this->geoipInfo = $GeoIPDto;
        }
        $this->showAjaxAnswer($this->geoipInfo);
    }

    private function isIp(string $str) {
        return preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $str);
    }

    private function checkAndHandleComponentParams() {
        
        $sendErrorToAdmin = (Bool) $this->arParams["SEND_ERROR_TO_ADMIN"];
        
        /**
         * Идентификатор хайлоад инфоболока
         */
        if (!$this->arParams["HLBL_ID"]) {
            $this->errorHandler(Loc::getMessage('HLBL_ID_IS_NOT_SET'), $sendErrorToAdmin);
        }

        /**
         * Ключ доступа апи ipstack.com
         */
        if (!$this->arParams["IPSTACK_COM_ACCESS_KEY"]) {
            $this->errorHandler(Loc::getMessage('IPSTACK_COM_ACCESS_KEY'), $sendErrorToAdmin);
        }
    }

    /**
     * Инициализация http клиента
     */
    private function initHttpClient() {
        return new HttpClient([
            "waitResponse" => true, // true - ждать ответа, false - отключаться после запроса
            "socketTimeout" => 30, // Таймаут соединения, сек
            "streamTimeout" => 60, // Таймаут чтения ответа, сек, 0 - без таймаута
            "version" => HttpClient::HTTP_1_0, // версия HTTP (HttpClient::HTTP_1_0 или HttpClient::HTTP_1_1)
            "compress" => false, // true - принимать gzip (Accept-Encoding: gzip)
            "charset" => "", // Кодировка тела для POST и PUT
            "disableSslVerification" => false, // true - отключить проверку ssl (с 15.5.9)
        ]);
    }

    private function getIp(string $ip)
    {
        return $this->httpClient->get('http://api.ipstack.com/'. $ip . '?access_key=' . $this->arParams["IPSTACK_COM_ACCESS_KEY"]);
    }

    private function getHighloadBlockEntity(int $hlblokId)
    {   
        $hlblock = HL\HighloadBlockTable::getById($hlblokId)->fetch(); 
        $entity = HL\HighloadBlockTable::compileEntity($hlblock); 
        
        return $entity->getDataClass(); 
    }

    private function addIpInfo(GeoIPDto $data): bool
    {
        $data = [
            "UF_IP" => $data->ip,
            "UF_TYPE"=> $data->type,
            "UF_CONTINENT_CODE"=> $data->continent_code,
            "UF_CONTINENT_NAME"=> $data->continent_name,
            "UF_COUNRTRY_CODE"=> $data->country_code,
            "UF_COUNRTRY_NAME"=> $data->country_name,
            "UF_REGION_CODE"=> $data->region_code,
            "UF_REGION_NAME"=> $data->region_name,
            "UF_CITY"=> $data->city,
            "UF_ZIP"=> $data->zip,
            "UF_LATITUDE"=> $data->latitude,
            "UF_LONGITUDE"=> $data->longitude,
        ];

        $result = $this->hlEntity::add($data);
        if (!$result) 
		{
			showError($this->hlEntity->LAST_ERROR);
			return false;
		}
        return true;
    }

    /**
     * Поиск ip в локальной базе
     *
     * @var string $ip ip адрес
     */
    private function findIpDataForHighloadBlock(string $ip)
    {
        $result = $this->hlEntity::getList([
            "select" => ["*"],
            "filter" => ["UF_IP" => $ip],
        ]);
        
        return $result->Fetch();
    }

    /**
     * Обработчик ошибок компоннента
     *
     * @var string $errorMessage сообщение об ошибке 
     * @var bool $sendToAdmin отправка сооющение об ошибке на почту администратора
     */
    private function errorHandler(string $errorMessage, bool $sendToAdmin = false): void {
        showError($errorMessage);
        if ($sendToAdmin) {
            bxmail(
                COption::GetOptionString("main", "email_from"),
                "Stroytest Geoip error",
                $errorMessage
            );
            }
    }

    private function showAjaxAnswer($result, int $responseCode = 200)
	{
		$this->app->RestartBuffer();
        http_response_code($responseCode);
		Header('Content-Type: application/json');
		echo json_encode($result);
		die();
	}
}
