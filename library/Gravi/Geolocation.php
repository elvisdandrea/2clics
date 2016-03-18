<?php
/**
 * Geolocation
 *
 * Get latitude/longitude or address using Google Maps API
 *
 * @author  Jeroen Desloovere   <info@jeroendesloovere.be>
 * @edited  Elvis D'Andrea      <elvis@vistasoft.com.br>
 */

class Gravi_Geolocation {

    /**
     * The Google API URL
     */
    const API_URL = 'http://maps.googleapis.com/maps/api/geocode/json';

    const IP_API = 'http://ip-api.com/json/';

    /**
     * Calls Google API
     *
     * @param  array  $parameters
     * @return array
     */
    private static function callAPI($parameters = array()) {

        $url = self::API_URL . '?';
        foreach ($parameters as $key => $value) $url .= $key . '=' . urlencode($value) . '&';
        $url = trim($url, '&');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);

        return $response->results;
    }

    /**
     * Get address using latitude/longitude
     *
     * @return array(label, street, streetNumber, city, cityLocal, zip, country, countryLabel)
     * @param  float        $latitude
     * @param  float        $longitude
     */
    public static function getAddress($latitude, $longitude) {

        $results = self::callAPI(array(
            'latlng' => $latitude . ',' . $longitude,
            'sensor' => 'false'
        ));

        return array(
            'label'         => (string) $results[0]->formatted_address,
            'street'        => (string) $results[0]->address_components[1]->short_name,
            'streetNumber'  => (string) $results[0]->address_components[0]->short_name,
            'city'          => (string) $results[0]->address_components[3]->short_name,
            'cityLocal'     => (string) $results[0]->address_components[2]->short_name,
            'zip'           => (string) $results[0]->address_components[7]->short_name,
            'country'       => (string) $results[0]->address_components[6]->short_name,
            'countryLabel'  => (string) $results[0]->address_components[6]->long_name
        );
    }

    /**
     * Get coordinates latitude/longitude
     *
     * @return array  The latitude/longitude coordinates
     * @param  string $street[optional]
     * @param  string $streetNumber[optional]
     * @param  string $city[optional]
     * @param  string $zip[optional]
     * @param  string $country[optional]
     */
    public static function getCoordinates($street = '', $streetNumber = '', $city = '', $zip = '', $country = '') {

        $address = implode(' ', func_get_args());

        $results = self::callAPI(array(
            'address' => $address,
            'sensor' => 'false'
        ));

        return array(
            'lat' => array_key_exists(0, $results) ? (float) $results[0]->geometry->location->lat : null,
            'lng' => array_key_exists(0, $results) ? (float) $results[0]->geometry->location->lng : null
        );
    }

    /**
     * Returns the location information from an IP
     *
     * @param   string          $ip     - The IP Address
     * @return  array|mixed
     */
    public static function getLocationFromIP($ip) {

        if (!filter_var($ip, FILTER_VALIDATE_IP))
            return array('message' => 'Invalid IP');

        $ch = curl_init(self::IP_API . $ip);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        return json_decode($result, true);
    }

    /**
     * Returns the location information from page visitor
     *
     * @return array|mixed
     */
    public static function getVisitorLocation() {
        $http     = new Zend_Controller_Request_Http();
        $ip       = $http->getServer('REMOTE_ADDR');
        return self::getLocationFromIP($ip);
    }
}