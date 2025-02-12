<?php

namespace OrangeHRM\ZkTeco\Controller\File;

use Exception;
use OrangeHRM\Core\Controller\AbstractFileController;
use OrangeHRM\Core\Traits\Service\TextHelperTrait;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Maintenance\DownloadFormats\DownloadFormat;
use OrangeHRM\ZkTeco\Api\Traits\ZkServiceTrait;
use OrangeHRM\ZkTeco\Api\Traits\ZkTecoCommonParamRuleCollection;
use OrangeHRM\ZkTeco\DownloadFormats\CsvDownloadFormat;
use OrangeHRM\ZkTeco\Reports\TransactionReport;

class AttendancePunchPairReportFileController extends AbstractFileController
{
    use ZkServiceTrait, ZkTecoCommonParamRuleCollection, TextHelperTrait;

    public function getDownloadFormat(string $type): DownloadFormat
    {
        if ($type == 'csv') {
            return new CsvDownloadFormat();
        }

        throw new Exception('Type not supported');
    }

    /**
     * @throws Exception
     */
    public function handle(Request $request): Response
    {
        try {
            $filterParams = $this->setPunchPairExportReportParams($request);

            $results = $this->getZkTecoService()->exportTransactions($filterParams);

            $downloadFormat = $this->getDownloadFormat($filterParams->getExportType());

            $content = $downloadFormat->getFormattedString(
                TransactionReport::filterColumns($filterParams->getExportColumns(), $results->getData())
            );

            $response = $this->getResponse();

            $this->setCommonHeadersToResponse(
                $downloadFormat->getDownloadFileName(sprintf('%s-%s', 'punch_pair', $filterParams->getDate())),
                sprintf('application/%s', $filterParams->getExportType()),
                $this->getTextHelper()->strLength($content),
                $response
            );
            $response->setContent($content);
            return $response;
        } catch (Exception) {
            return $this->handleBadRequest();
        }
    }
}