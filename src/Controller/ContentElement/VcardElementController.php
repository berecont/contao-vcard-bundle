<?php

namespace Berecont\ContaoVcardBundle\Controller\ContentElement;

use Berecont\ContaoVcardBundle\Model\VcardModel;
use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsContentElement(
    type: 'vcard',
    category: 'miscellaneous',
    template: 'content_element/vcard',
)]
class VcardElementController extends AbstractContentElementController
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    protected function getResponse(
        FragmentTemplate $template,
        ContentModel $model,
        Request $request,
    ): Response {
        $vcard = VcardModel::findByPk($model->vcard);

        if (null === $vcard) {
            return new Response();
        }

        $template->vcard = $vcard;
        $template->formattedName = $vcard->getFormattedName();

        $template->downloadUrl = $this->urlGenerator->generate(
            'contao_vcard',
            ['id' => $vcard->id],
        );

        return $template->getResponse();
    }
}