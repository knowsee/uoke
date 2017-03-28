<?php
namespace Services;
class TradeInfo {
    const TABLE_NAME = 'hzmz_tradeok';
    const TABLE_PY = 'id';
    const TABLE_ORDER = array(
        'id' => 'DESC',
    );
    use \Services\TraitService;
}
