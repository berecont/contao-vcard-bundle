<?php

namespace Berecont\ContaoVcardBundle\Controller\ContentElement;

use Berecont\ContaoVcardBundle\Helper\VcardHelper;
use Berecont\ContaoVcardBundle\Model\VcardModel;
use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\StringUtil;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;
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

        $template->formattedName = $vcard->getFormattedName();
        $template->photo = $vcard->photo;
        $template->size = $model->size;
        $template->jobtitle = $vcard->jobtitle;
        $template->role = $vcard->role;

        $template->phones = StringUtil::deserialize($vcard->phones, true);
        $template->emailAddresses = StringUtil::deserialize($vcard->emailAddresses, true);

        $template->showImage = (bool) $model->vcardShowImage;
        $template->showName = (bool) $model->vcardShowName;
        $template->showRole = (bool) $model->vcardShowRole;
        $template->showPhones = (bool) $model->vcardShowPhones;
        $template->showEmails = (bool) $model->vcardShowEmails;
        $template->showDownload = (bool) $model->vcardShowDownload;
        $template->showQrCode = (bool) $model->vcardShowQrCode;

        $downloadUrl = $this->urlGenerator->generate(
            'contao_vcard',
            ['id' => $vcard->id],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        $template->downloadUrl = $downloadUrl;
        $template->qrCode = null;

        if ($template->showQrCode) {
            $vcardData = VcardHelper::generateVcard(
                $vcard,
                $downloadUrl,
                includeImages: false,
            );

            $qrCode = new QrCode(
                data: $vcardData,
                size: (int) $model->vcardQrCodeSize ?: 300,
                margin: 10,
            );

            $writer = new SvgWriter();
            $result = $writer->write($qrCode);

            $template->qrCode = $result->getString();
        }

        return $template->getResponse();
    }
}