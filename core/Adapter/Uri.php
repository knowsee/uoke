<?php
namespace Adapter;

interface Uri {
    /**
     * Url rule Info
     * @return mixed
     */
    public function getUrlModel();
    public function getRule();
    public function setRule();
    public function makeUrl($param, $urlName = '');

}