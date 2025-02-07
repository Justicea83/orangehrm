<?php

namespace OrangeHRM\ZkTeco\Controller;

use OrangeHRM\Admin\Service\PayGradeService;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Controller\Exception\VueControllerException;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Services;
use OrangeHRM\I18N\Traits\Service\I18NHelperTrait;

class CreateConnectionController extends AbstractVueController
{
    use ServiceContainerTrait;
    use I18NHelperTrait;

    /**
     * @return PayGradeService
     */
    public function getPayGradeService(): PayGradeService
    {
        return $this->getContainer()->get(Services::PAY_GRADE_SERVICE);
    }

    /**
     * @throws DaoException
     * @throws VueControllerException
     */
    public function preRender(Request $request): void
    {
        $component = new Component('create-connection');

        $currencies = $this->getPayGradeService()->getCurrencyArray();
        $paygrades = $this->getPayGradeService()->getPayGradeArray();
        $payFrequencies = $this->getPayGradeService()->getPayPeriodArray();

        $component->addProp(new Prop('currencies', Prop::TYPE_ARRAY, $currencies));
        $component->addProp(new Prop('paygrades', Prop::TYPE_ARRAY, $paygrades));
        $component->addProp(
            new Prop(
                'pay-frequencies',
                Prop::TYPE_ARRAY,
                array_map(
                    fn (array $payFrequencies) => [
                        'id' => $payFrequencies['id'],
                        'label' => $this->getI18NHelper()->transBySource($payFrequencies['label'])
                    ],
                    $payFrequencies
                )
            )
        );

        $this->setComponent($component);
    }
}