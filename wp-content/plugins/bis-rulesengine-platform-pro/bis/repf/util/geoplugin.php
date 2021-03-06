<?php

/*
  This PHP class is free software: you can redistribute it and/or modify
  the code under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  However, the license header, copyright and author credits
  must not be modified in any form and always be displayed.

  This class is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  @author geoPlugin (gp_support@geoplugin.com)
  @copyright Copyright geoPlugin (gp_support@geoplugin.com)
  $version 1.01


  This PHP class uses the PHP Webservice of http://www.geoplugin.com/ to geolocate IP addresses

  Geographical location of the IP address (visitor) and locate currency (symbol, code and exchange rate) are returned.

  See http://www.geoplugin.com/webservices/php for more specific details of this free service

 */

namespace bis\repf\util;

class geoPlugin {

    //the geoPlugin server
    var $host = 'http://www.geoplugin.net/php.gp?ip={IP}&base_currency={CURRENCY}';
    //the default base currency
    var $currency = 'USD';
    //initiate the geoPlugin vars
    var $ip = null;
    var $countryCode = null;
    var $countryName = null;
    var $continentCode = null;
    var $latitude = null;
    var $longitude = null;
    var $currencyCode = null;
    var $currencySymbol = null;
    var $currencyConverter = null;
    var $city = null;
    var $region = null;

    function geoPlugin() {
        
    }

    function locate($ip = null) {

        global $_SERVER;

        if (is_null($ip)) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $host = str_replace('{IP}', $ip, $this->host);
        $host = str_replace('{CURRENCY}', $this->currency, $host);

        $data = array();

        $response = $this->fetch($host);


        $data = unserialize($response);

        //set the geoPlugin vars
        $this->ip = $ip;
        $this->countryCode = $data['geoplugin_countryCode'];
        $this->countryName = $data['geoplugin_countryName'];
        $this->continentCode = $data['geoplugin_continentCode'];
        $this->currencyCode = $data['geoplugin_currencyCode'];
        $this->city = $data['geoplugin_city'];
        $this->region = $data['geoplugin_region'];
        $this->currencySymbol = $data['geoplugin_currencySymbol'];
        $this->currencyConverter = $data['geoplugin_currencyConverter'];
        $this->latitude = $data['geoplugin_latitude'];
        $this->longitude = $data['geoplugin_longitude'];
    }

    function fetch($host) {

        if (function_exists('curl_init')) {

            //use cURL to fetch data
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $host);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'geoPlugin PHP Class v1.0');
            $response = curl_exec($ch);
            curl_close($ch);
        } else if (ini_get('allow_url_fopen')) {

            //fall back to fopen()
            $response = file_get_contents($host, 'r');
        } else {

            trigger_error('geoPlugin class Error: Cannot retrieve data. Either compile PHP with cURL support or enable allow_url_fopen in php.ini ', E_USER_ERROR);
            return;
        }

        return $response;
    }

}

?>
