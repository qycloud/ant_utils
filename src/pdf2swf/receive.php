<?php
namespace Utils\Pdf2swf;
use Utils\Pdf2swf\Pdf2swf;

/**
 * Resque to convert
 *
 * @package   Resque,pdf2swf
 * @author    Mtao <jmtao33@gmail.com>
 * @copyright Copyright (C) 2012 Safirst Technology (www.a-y.com.cn)
 */

class Receive
{
    public $filePath = '';
    public $fileName = '';
    public function __construct($filePath, $fileName)
    {
        $this->filePath = $filePath;
        $this->fileName = $fileName;
    }

    public function init($extName)
    {
        $filePath = $this->filePath . ($this->fileName ? DIRECTORY_SEPARATOR . $this->fileName : '');
        $convertPath = self::convertPdf(realpath($filePath), $extName);
        if (!is_array($convertPath)) {
            return $convertPath;
        }
        return self::pdf2swf($convertPath);
    }


    public static function getPdfName($filePath, $extName = '')
    {

        $fName = pathinfo($filePath, PATHINFO_BASENAME);
        if ($extName == 'pdf') {
            return $fName;
        } else {
            return $fName . '.pdf';
        }

    }


    public static function getSwfName($filePath, $extName = '')
    {
        return self::getPdfName($filePath, $extName) . '.swf';
    }



    public static function convertPdf($filePath, $extName = '')
    {

        if (!file_exists($filePath)) {
            return '文件已删除或不存在';
        }

        $dirName = pathinfo($filePath, PATHINFO_DIRNAME);
        $fName   = pathinfo($filePath, PATHINFO_BASENAME);

        $convertPath = array(
            'pdfPath' => $dirName . DS,
            'swfPath' => $dirName . DS,
            'pdfName' => self::getPdfName($filePath, $extName),
            'page' => '',
            'callback' => '',
            'format' => 'swf'

        );


        if ($extName != 'pdf') {
            self::_convertPdf($dirName, $fName, $convertPath['pdfName']);
        }

        return $convertPath;
    }

    public static function _convertPdf($dirName, $fName, $pdfName)
    {

        if (!file_exists($pdfName)) {
            $oldDir = getcwd();
            if (!chdir($dirName)) {
                return;
            }

            if (ISWIN) {
                shell_exec("soffice --convert-to pdf $fName --headless");
            } else {
                shell_exec("HOME=/tmp libreoffice --convert-to pdf $fName --headless");
            }


            chdir($oldDir);
        }
    }


    public static function renderPdf($swfFilePath)
    {

        $pdf2swf = new Pdf2swf();

        if ($pdf2swf->getConfig('allowcache')) {
            $pdf2swf->setCacheHeaders();
        }
        if (!$pdf2swf->getConfig('allowcache')
            || ($pdf2swf->getConfig('allowcache')
            && $pdf2swf->endOrRespond())) {
            header('Content-type: application/x-shockwave-flash');
            header('Accept-Ranges: bytes');
            header('Content-Length: ' . filesize($swfFilePath));
            die(file_get_contents($swfFilePath));
        }

    }


    public static function pdf2swf($convertPath)
    {
        $pdf2swf = new Pdf2swf();
        //路径全局配置
        $pdf2swf->setConfig(
            array(
                'path.pdf' => $convertPath['pdfPath'],
                'path.swf' => $convertPath['swfPath']
            )
        );

        $page     = !empty($convertPath['page']) ? $convertPath['page'] : "";
        $callback = !empty($convertPath['callback']) ? $convertPath['callback'] : "";
        $pdfdoc   = $convertPath['pdfName'];
        $format   = !empty($convertPath['format']) ? $convertPath['format'] : "swf";
        $swfdoc   = $pdfdoc . $page .  ".swf";

        $pngdoc          = $pdfdoc . "_" . $page . ".png";
        $messages        = "";
        $swfFilePath     = $pdf2swf->getConfig('path.swf') . $swfdoc;
        $pdfFilePath     = $pdf2swf->getConfig('path.pdf') . $pdfdoc;
        $pngFilePath     = $pdf2swf->getConfig('path.swf') . $pngdoc;
        $validatedConfig = true;

        if (!is_dir($pdf2swf->getConfig('path.swf'))) {
            $validatedConfig = false;
        }
        if (!is_dir($pdf2swf->getConfig('path.pdf'))) {
            $validatedConfig = false;
        }
        if (!$validatedConfig) {
            return '找不到要转换目录';
        } else if (!$pdf2swf->validPdfParams($pdfFilePath, $pdfdoc, $page) ) {
            return '检查您的路径是否正确';
        } else {
            if ($format == "swf" || $format == "png" || $format == "pdf") {

                if (file_exists($swfFilePath)) {
                    return [true, $swfFilePath];
                } else {

                    $messages = $pdf2swf->convert($pdfdoc, $page);

                    if ($messages === true) {
                        return [true, $swfFilePath];
                    }

                    if (strlen($messages) == 0) {
                        return '找不到swf文件,请检查配置路径是否正确';
                    }
                }
            }

        }
    }
}
