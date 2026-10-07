<?php

use Berecont\ContaoVcardBundle\Model\VcardModel;

$GLOBALS['TL_DCA']['tl_content']['palettes']['vcard'] =
    '{type_legend},type,headline;'
    . '{vcard_legend},vcard;'
    . '{template_legend:hide},customTpl;'
    . '{expert_legend:hide},cssID;'
    . '{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['vcard'] = [
    'inputType' => 'select',

    'options_callback' => static function (): array {
        $options = [];

        $vcards = VcardModel::findAll([
            'order' => 'lastname ASC, firstname ASC',
        ]);

        if (null === $vcards) {
            return $options;
        }

        foreach ($vcards as $vcard) {
            $options[$vcard->id] = $vcard->getFormattedName();
        }

        return $options;
    },

    'eval' => [
        'mandatory' => true,
        'chosen' => true,
        'includeBlankOption' => true,
        'tl_class' => 'w50',
    ],

    'sql' => "int(10) unsigned NOT NULL default '0'",
];