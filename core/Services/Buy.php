<?php
namespace Services;
class Buy {
    const TABLE_NAME = 'hzmz_buyin_box';
    const TABLE_PY = 'id';
    const TABLE_ORDER = array(
        'trade_evg_price' => 'DESC',
    );
    use \Services\TraitService;
}
