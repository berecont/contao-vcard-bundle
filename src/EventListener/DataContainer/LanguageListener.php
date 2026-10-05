<?php
namespace Berecont\ContaoVcardBundle\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Intl\Locales;

#[AsCallback(table: 'tl_vcard', target: 'fields.language.options')]
class LanguageListener {
    public function __construct(private readonly Locales $locales) {
    }

    public function __invoke(): array {
        return $this->locales->getLocales();
    }
}
