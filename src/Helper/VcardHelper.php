<?php
namespace Berecont\ContaoVcardBundle\Helper;

use Contao\FilesModel;
use Contao\StringUtil;
use Berecont\ContaoVcardBundle\Model\VcardModel;

class VcardHelper {
    public static function generateVcard(VcardModel $vcard, string $sourceURL): string {
        $data = "BEGIN:VCARD\r\n";
        $data .= "VERSION:4.0\r\n";
        $data .= "PRODID:-//Contao//berecont/contao-vcard-bundle//EN\r\n";
        $data .= 'REV:'.date('Ymd\This\Z', $vcard->tstamp)."\r\n";
        $data .= 'SOURCE:'.$sourceURL."\r\n";
        $data .= 'KIND:'.$vcard->kind."\r\n";
        $data .= 'FN:'.$vcard->getFormattedName()."\r\n";

        $data .= self::generateGeneralData($vcard);

        switch ($vcard->kind) {
            case 'individual':
                $data .= self::generateIndividualData($vcard);
                break;
        }

        $data .= "END:VCARD\r\n";

        return $data;
    }

    private static function generateIndividualData(VcardModel $vcard): string {
        $data = '';
        $data .= 'N:'.$vcard->lastname.';'.$vcard->firstname.';'.$vcard->additionalNames.';'.$vcard->honoricPrefixes.';'.$vcard->honoricSuffixes."\r\n";
        if ($vcard->gender) {
            $data .= 'GENDER:'.$vcard->gender."\r\n";
        }
        if ($vcard->dateOfBirth) {
            $data .= 'BDAY:'.date('Ymd', $vcard->dateOfBirth)."\r\n";
        }
        if ($vcard->nickname) {
            $data .= 'NICKNAME:'.$vcard->nickname."\r\n";
        }
        if ($vcard->jobtitle) {
            $data .= 'TITLE:'.$vcard->jobtitle."\r\n";
        }
        if ($vcard->role) {
            $data .= 'ROLE:'.$vcard->role."\r\n";
        }
        if ($vcard->note) {
            $data .= 'NOTE:'.$vcard->note."\r\n";
        }

        if ($vcard->calendarURL) {
            $data .= 'CALURI:'.$vcard->calendarURL."\r\n";
        }
        if ($vcard->calendarRequestURL) {
            $data .= 'CALADRURI:'.$vcard->calendarRequestURL."\r\n";
        }
        if ($vcard->calendarFreeBusyURL) {
            $data .= 'FBURL:'.$vcard->calendarFreeBusyURL."\r\n";
        }
        if ($vcard->tags) {
            $data .= 'CATEGORIES:'.implode(',', StringUtil::deserialize($vcard->tags))."\r\n";
        }

        if ($vcard->photo) {
            $file = FilesModel::findByPk(StringUtil::binToUuid($vcard->photo));
            if ($file) {
                $type = mime_content_type($file->getAbsolutePath());
                $base64 = base64_encode(file_get_contents($file->getAbsolutePath()));
                $data .= 'PHOTO;ENCODING=b;TYPE='.strtoupper($type).':'.$base64."\r\n";
            }
        }

        return $data;
    }

    private static function generateGeneralData(VcardModel $vcard): string {
        $data = '';
        if ($vcard->language) {
            $data .= 'LANG:'.$vcard->language."\r\n";
        }
        if ($vcard->timezone) {
            $data .= 'TZ:'.$vcard->timezone."\r\n";
        }

        if ($vcard->company) {
            $data .= 'ORG:'.$vcard->company."\r\n";
        }

        if ($vcard->logo) {
            $file = FilesModel::findByPk(StringUtil::binToUuid($vcard->logo));
            if ($file) {
                $type = mime_content_type($file->getAbsolutePath());
                $base64 = base64_encode(file_get_contents($file->getAbsolutePath()));
                $data .= 'LOGO;ENCODING=b;TYPE='.strtoupper($type).':'.$base64."\r\n";
            }
        }

        foreach (StringUtil::deserialize($vcard->addresses) as $address) {
            $data .= 'ADR;TYPE='.strtoupper($address['type']).':'
                .$address['pobox'].';'
                .$address['extended'].';'
                .$address['street'].';'
                .$address['city'].';'
                .$address['state'].';'
                .$address['zip'].';'
                .$address['country']
                ."\r\n";
        }

        foreach (StringUtil::deserialize($vcard->phones) as $phone) {
            $data .= 'TEL;TYPE='.strtoupper($phone['type']).':'.$phone['number']."\r\n";
        }
        foreach (StringUtil::deserialize($vcard->emailAddresses) as $email) {
            $data .= 'EMAIL;TYPE='.strtoupper($email['type']).':'.$email['email']."\r\n";
        }
        foreach (StringUtil::deserialize($vcard->urls) as $url) {
            $data .= 'URL;TYPE='.strtoupper($url['type']).':'.$url['url']."\r\n";
        }

        return $data;
    }
}
