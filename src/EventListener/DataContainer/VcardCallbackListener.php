<?php
namespace Berecont\ContaoVcardBundle\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\Input;
use Contao\Message;
use Berecont\ContaoVcardBundle\Model\VcardModel;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class VcardCallbackListener {
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly RequestStack $requestStack,
    ) {
    }

    #[AsCallback(table: 'tl_vcard', target: 'list.label.label')]
    public function label(array $row, string $label, DataContainer $dc, array $labels): array {
        $model = VcardModel::findByPk($row['id']);
        if (null === $model) {
            return $labels;
        }

        $labels = [];
        $labels[] = $model->getFormattedName();
        $labels[] = $row['kind'] ? '<span class="tl_gray">['.$GLOBALS['TL_LANG']['tl_vcard']['kind_options'][$row['kind']].']</span>' : '';

        return $labels;
    }

    #[AsCallback(table: 'tl_vcard', target: 'config.onload')]
    public function adjustEditView(DataContainer $dc): void {
        $url = $this->requestStack->getCurrentRequest()?->getSchemeAndHttpHost().'/_contao/vcard/'.$dc->id;
        if ('edit' !== Input::get('act')) {
            return;
        }

        Message::addInfo(\sprintf(
            '%s: %s',
            $this->translator->trans('tl_vcard.hintEdit', [], 'contao_tl_vcard'),
            $url,
        ));
    }
}
