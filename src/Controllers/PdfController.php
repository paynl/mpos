<?php

namespace MPOS\Controllers;

use Mpdf\Mpdf;
use MPOS\Exceptions\ConfigException;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use Mpdf\MpdfException;

class PdfController extends BaseController
{
    /**
     * @param ServerRequest $request
     * @return Response
     * @throws ConfigException
     * @throws MpdfException
     */
    public function generatePDF(ServerRequest $request): Response
    {
        $requestBody = (array)$request->getParsedBody();
        $base64Content = (string)($requestBody['base64'] ?? '');
        $decodedContent = (string)base64_decode($base64Content);
        $decodedContent = nl2br($decodedContent);
        $decodedContent = str_replace(' ', '&nbsp;', $decodedContent);
        $tempDir = $this->getConfig('common.temp_path');

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'default_font_size' => 6.3,
            'format' => [69, 89],
            'margin_left' => 3,
            'margin_right' => 0,
            'margin_top' => 8,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
            'tempDir' => $tempDir
        ]);

        $mpdf->useTibetanLBR = false;
        $mpdf->WriteHTML('<pre>' . $decodedContent . '</pre>');
        $pdf = $mpdf->Output('receipt.pdf', "S");

        return $this->response(base64_encode($pdf));
    }
}
