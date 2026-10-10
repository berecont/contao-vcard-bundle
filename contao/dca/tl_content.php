<?php

use Berecont\ContaoVcardBundle\Model\VcardModel;

$GLOBALS['TL_DCA']['tl_content']['palettes']['vcard'] =
    '{type_legend},type,headline,title;'
    . '{vcard_legend},vcard,size;'
    . '{vcard_display_legend},vcardShowImage,vcardShowName,vcardShowRole,vcardShowPhones,vcardShowEmails,vcardShowDownload,vcardShowQrCode,vcardQrCodeSize;'
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

$GLOBALS['TL_DCA']['tl_content']['fields']['vcardShowImage'] = [
    'inputType' => 'checkbox',
    'eval' => [
        'tl_class' => 'w33',
    ],
    'sql' => [
        'type' => 'boolean',
        'default' => true,
    ],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['vcardShowName'] = [
    'inputType' => 'checkbox',
    'eval' => [
        'tl_class' => 'w33',
    ],
    'sql' => [
        'type' => 'boolean',
        'default' => true,
    ],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['vcardShowRole'] = [
    'inputType' => 'checkbox',
    'eval' => [
        'tl_class' => 'w33',
    ],
    'sql' => [
        'type' => 'boolean',
        'default' => true,
    ],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['vcardShowPhones'] = [
    'inputType' => 'checkbox',
    'eval' => [
        'tl_class' => 'w33',
    ],
    'sql' => [
        'type' => 'boolean',
        'default' => true,
    ],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['vcardShowEmails'] = [
    'inputType' => 'checkbox',
    'eval' => [
        'tl_class' => 'w33',
    ],
    'sql' => [
        'type' => 'boolean',
        'default' => true,
    ],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['vcardShowDownload'] = [
    'inputType' => 'checkbox',
    'eval' => [
        'tl_class' => 'w33',
    ],
    'sql' => [
        'type' => 'boolean',
        'default' => true,
    ],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['vcardShowQrCode'] = [
    'inputType' => 'checkbox',
    'eval' => [
        'tl_class' => 'w33',
    ],
    'sql' => [
        'type' => 'boolean',
        'default' => true,
    ],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['vcardQrCodeSize'] = [
    'inputType' => 'text',
    'default' => 300,
    'eval' => [
        'rgxp' => 'natural',
        'maxlength' => 4,
        'tl_class' => 'w33',
    ],
    'sql' => [
        'type' => 'integer',
        'unsigned' => true,
        'default' => 300,
    ],
];