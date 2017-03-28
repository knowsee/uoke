<?php
namespace Services;
class Sell {
    const TABLE_NAME = 'hzmz_sellout_box';
    const TABLE_PY = 'id';
    const TABLE_ORDER = array(
        'trade_evg_price' => 'ASC',
    );
    use \Services\TraitService;
    
}
