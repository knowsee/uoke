<?php
namespace Services;
class ClientCall {
    const TABLE_NAME = 'ClientList';
    const TABLE_PY = 'id';
    const TABLE_ORDER = array(
        'id' => 'DESC',
    );
    use \Services\TraitService;
}
