<?php

namespace Berecont\ContaoVcardBundle\Helper;

use Berecont\ContaoVcardBundle\Model\VcardModel;
use Contao\FilesModel;
use Contao\StringUtil;

class VcardHelper 
{
    public static function generateVcard(
        VcardModel $vcard,
        string $sourceURL,
        bool $includeImages = true,
    ): string {
        $data = "BEGIN:VCARD\r\n";
        $data .= "VERSION:4.0\r\n";
        $data .= "PRODID:-//Contao//berecont/contao-vcard-bundle//EN\r\n";
        $data .= 'REV:'.date('Ymd\This\Z', $vcard->tstamp)."\r\n";
        $data .= 'SOURCE:'.$sourceURL."\r\n";
        $data .= 'KIND:'.$vcard->kind."\r\n";
        $data .= 'FN:'.self::escapeText($vcard->getFormattedName())."\r\n";

        $data .= self::generateGeneralData($vcard, $includeImages);

        if ('individual' === $vcard->kind) {
            $data .= self::generateIndividualData($vcard, $includeImages);
        }

        $data .= "END:VCARD\r\n";

        $lines = explode("\r\n", $data);
        $lines = array_map(self::foldLine(...), $lines);

        return implode("\r\n", $lines);
    }

    private static function generateIndividualData(
        VcardModel $vcard,
        bool $includeImages = true,
    ): string {
        $data = '';
        $data .= 'N:'.implode(';', [
            self::escapeText($vcard->lastname),
            self::escapeText($vcard->firstname),
            self::escapeText($vcard->additionalNames),
            self::escapeText($vcard->honoricPrefixes),
            self::escapeText($vcard->honoricSuffixes),
        ])."\r\n";        
        if ($vcard->gender) {
            $data .= 'GENDER:'.$vcard->gender."\r\n";
        }
        if ($vcard->dateOfBirth) {
            $data .= 'BDAY:'.date('Ymd', $vcard->dateOfBirth)."\r\n";
        }
        if ($vcard->nickname) {
            $data .= 'NICKNAME:'.self::escapeText($vcard->nickname)."\r\n";
        }

        if ($vcard->jobtitle) {
            $data .= 'TITLE:'.self::escapeText($vcard->jobtitle)."\r\n";
        }

        if ($vcard->role) {
            $data .= 'ROLE:'.self::escapeText($vcard->role)."\r\n";
        }

        if ($vcard->note) {
            $note = $vcard->note;

            $note = preg_replace('~<br\s*/?>~i', "\n", $note);
            $note = preg_replace('~</p\s*>~i', "\n", $note);

            $note = strip_tags($note);
            $note = html_entity_decode($note, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            $note = trim($note);

            $data .= 'NOTE:'.self::escapeText($note)."\r\n";
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
            $tags = StringUtil::deserialize($vcard->tags, true);

            $tags = array_map(
                self::escapeText(...),
                $tags,
            );

            $data .= 'CATEGORIES:'.implode(',', $tags)."\r\n";
        }

        if ($includeImages && $vcard->photo) {
            $data .= self::generateImageData('PHOTO', $vcard->photo);
        }

        return $data;
    }

    private static function generateGeneralData(
        VcardModel $vcard,
        bool $includeImages = true,
    ): string {
        $data = '';
        if ($vcard->language) {
            $data .= 'LANG:'.$vcard->language."\r\n";
        }
        if ($vcard->timezone) {
            $data .= 'TZ:'.$vcard->timezone."\r\n";
        }

        if ($vcard->company) {
            $data .= 'ORG:'.self::escapeText($vcard->company)."\r\n";
        }

        if ($includeImages && $vcard->logo) {
            $data .= self::generateImageData('LOGO', $vcard->logo);
        }

        foreach (StringUtil::deserialize($vcard->addresses, true) as $address) {
            $data .= 'ADR;TYPE='.strtoupper($address['type']).':'
                .implode(';', [
                    self::escapeText($address['pobox'] ?? ''),
                    self::escapeText($address['extended'] ?? ''),
                    self::escapeText($address['street'] ?? ''),
                    self::escapeText($address['city'] ?? ''),
                    self::escapeText($address['state'] ?? ''),
                    self::escapeText($address['zip'] ?? ''),
                    self::escapeText($address['country'] ?? ''),
                ])."\r\n";
        }

        foreach (StringUtil::deserialize($vcard->phones, true) as $phone) {
            $data .= 'TEL;TYPE='.strtoupper($phone['type']).':'
                .self::escapeText($phone['number'] ?? '')."\r\n";
        }

        foreach (StringUtil::deserialize($vcard->emailAddresses, true) as $email) {
            $data .= 'EMAIL;TYPE='.strtoupper($email['type']).':'
                .($email['email'] ?? '')."\r\n";
        }

        foreach (StringUtil::deserialize($vcard->urls, true) as $url) {
            $data .= 'URL;TYPE='.strtoupper($url['type']).':'
                .($url['url'] ?? '')."\r\n";
        }

        return $data;
    }

    private static function generateImageData(
        string $property,
        ?string $uuid,
    ): string {
        if (!$uuid) {
            return '';
        }

        $file = FilesModel::findByPk(StringUtil::binToUuid($uuid));

        if (null === $file) {
            return '';
        }

        $path = $file->getAbsolutePath();

        if (!is_file($path) || !is_readable($path)) {
            return '';
        }

        $content = file_get_contents($path);
        $type = mime_content_type($path);

        if (false === $content || false === $type) {
            return '';
        }

        return $property.':data:'.$type.';base64,'
            .base64_encode($content)."\r\n";
    }  
    
    private static function escapeText(?string $value): string
    {
        if (null === $value) {
            return '';
        }

        return str_replace(
            ['\\', "\r\n", "\r", "\n", ';', ','],
            ['\\\\', '\\n', '\\n', '\\n', '\\;', '\\,'],
            $value,
        );
    }  
    
    private static function foldLine(string $line): string
    {
        $result = '';
        $limit = 75;

        while (strlen($line) > $limit) {
            $chunk = mb_strcut($line, 0, $limit, 'UTF-8');

            $result .= $chunk."\r\n ";
            $line = substr($line, strlen($chunk));

            $limit = 74;
        }

        return $result.$line;
    }    
}
