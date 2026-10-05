<?php
namespace Berecont\ContaoVcardBundle\Model;

use Contao\Model;

class VcardModel extends Model {
    protected static $strTable = 'tl_vcard';

    public function getFormattedName(): string {
        if ('org' == $this->kind) {
            return $this->organizationName;
        }
        $name = [];
        if ($this->honoricPrefixes) {
            $name[] = $this->honoricPrefixes;
        }
        if ($this->firstname) {
            $name[] = $this->firstname;
        }
        if ($this->additionalNames) {
            $name[] = $this->additionalNames;
        }
        if ($this->lastname) {
            $name[] = $this->lastname;
        }
        if ($this->honoricSuffixes) {
            $name[] = $this->honoricSuffixes;
        }

        return implode(' ', $name);
    }
}
