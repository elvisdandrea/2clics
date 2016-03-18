<?php


class Gravi_Service_ImochatService {


    public static function SaveSearch($filters, $params) {

        if(empty($params['page'])){
            $session = new Zend_Session_Namespace('user_data');
            $jsonFilters = json_encode($filters);
            $hashedFilters = md5($jsonFilters);

            if(!is_array($session->searches))
                $session->searches = array();

            if(!in_array($hashedFilters, $session->searches)){
                $imochatService = new Gravi_Service_Imochat();
                $imochatService->saveSearch($filters);
                $session->searches[] = $hashedFilters;
            }
        }

    }

    public static function SaveSiteView() {
        $imochatService = new Gravi_Service_Imochat();
        $imochatService->saveSiteView();
    }

    public static function SaveSiteContact($params) {
        $imochatService = new Gravi_Service_Imochat();
        $imochatService->saveSiteContact($params);
    }

    public static function SaveSiteOffer($params) {
        $imochatService = new Gravi_Service_Imochat();
        $imochatService->saveSiteOffer($params);
    }

    public static function SavePropertyView($id) {

        $imochatService = new Gravi_Service_Imochat();
        $imochatService->savePropertyView($id);
    }

    public static function getBanners() {
        $imochatService = new Gravi_Service_Imochat();
        return $imochatService->getBanners();
    }

    public static function getBairros($id = false) {
        $imochatService = new Gravi_Service_Imochat();
        return $imochatService->getBairros($id);
    }


    public static function getSiteConfig() {
        $imochatService = new Gravi_Service_Imochat();
        return $imochatService->getSiteConfig();
    }

    public static function getUsers() {
        $imochatService = new Gravi_Service_Imochat();
        return $imochatService->getUsers();
    }


}