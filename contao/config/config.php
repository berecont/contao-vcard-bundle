<?php

use Berecont\ContaoVcardBundle\Model\VcardModel;

$GLOBALS['BE_MOD']['content']['vcard'] = [
    'tables' => ['tl_vcard'],
];

$GLOBALS['TL_MODELS']['tl_vcard'] = VcardModel::class;
