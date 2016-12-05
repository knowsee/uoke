<?php
namespace Uoke\Request\TraitClass;
use Helper\Filter;
trait TraitClient {

    public function filter($mixed) {
        return Filter::Check($mixed, Filter::INPUT_CHECK);
    }

}