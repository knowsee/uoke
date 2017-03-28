<?php

namespace Config\TraitConfig;

trait Route {

    public function routeIndex() {
        if (file_exists_case(LOAD_CONFIG . 'route.php')) {
            require LOAD_CONFIG . 'route.php';
            if (is_array($route)) {
                return $route;
            } else {
                return false;
            }
        }
        return false;
    }

}
