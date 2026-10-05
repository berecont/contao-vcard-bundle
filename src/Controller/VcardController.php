<?php
namespace Berecont\ContaoVcardBundle\Controller;

use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\Framework\ContaoFramework;
use Berecont\ContaoVcardBundle\Helper\VcardHelper;
use Berecont\ContaoVcardBundle\Model\VcardModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/_contao/vcard/{id}', name: 'contao_vcard', methods: ['GET'], requirements: ['id' => '\d+'])]
class VcardController extends AbstractController {
    public function __construct(private readonly ContaoFramework $contaoFramework) {
    }

    public function __invoke(int $id): Response {
        $this->contaoFramework->initialize();
        $vcard = VcardModel::findByPk($id);
        if (!$vcard) {
            throw new PageNotFoundException('VCard not found');
        }

        $vcardData = VcardHelper::generateVcard($vcard, $this->generateUrl('contao_vcard', ['id' => $vcard->id], UrlGeneratorInterface::ABSOLUTE_URL));

        return new Response($vcardData, 200, [
            'Content-Type' => 'text/vcard; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$vcard->getFormattedName().'.vcf"',
        ]);
    }
}
