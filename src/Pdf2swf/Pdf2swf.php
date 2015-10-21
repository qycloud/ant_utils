<?php
namespace Utils\Pdf2swf;
/**
 * pdf to swf
 *
 * @package   swftool
 * @author    Mtao <jmtao33@gmail.com>
 * @copyright Copyright (C) 2012 Safirst Technology (www.a-y.com.cn)
 */
class Pdf2swf extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function convert($doc, $page)
    {
        $output = array();
        $pdfFilePath = $this->getConfig('path.pdf') . $doc;
        $swfFilePath = $this->getConfig('path.swf') . $doc  . $page. ".swf";
        if ($this->getConfig('splitmode')) {
            $command = $this->getConfig('cmd.conversion.splitpages');
        } else {
            $command = $this->getConfig('cmd.conversion.singledoc');
        }
        $command = str_replace("{path.pdf}", $this->getConfig('path.pdf'), $command);
        $command = str_replace("{path.swf}", $this->getConfig('path.swf'), $command);
        $command = str_replace("{pdffile}", $doc, $command);
        if (!$this->isNotConverted($pdfFilePath, $swfFilePath)) {
            array_push($output, utf8_encode("[Converted]"));
            return true;
        }
        $return_var = 0;
        if ($this->getConfig('splitmode')) {
            $pagecmd = str_replace("%", $page, $command);
            $pagecmd = $pagecmd . " -p " . $page;
            exec($pagecmd, $output, $return_var);
            $hash = $this->getStringHashCode($command);
            if (!isset($_SESSION['CONVERSION_' . $hash])) {
                exec($this->getForkCommandStart() . $command . $this->getForkCommandEnd());
                $_SESSION['CONVERSION_' . $hash] = true;
            }
        } else {
            exec($command, $output, $return_var);
        }
        if ($return_var == 0 || strstr(strtolower($return_var), "notice")) {
            $s = true;
        } else {
            $s = "Error converting document" . $this->getDocUrl();
        }
        return $s;
    }

    public function isNotConverted($pdfFilePath,$swfFilePath)
    {
        if (!file_exists($pdfFilePath)) {
            return "Document.does.not.exist";
        }
        if ($swfFilePath == null) {
            return "Document.output.file.name.not.set";
        } else {
            if (!file_exists($swfFilePath)) {
                return true;
            } else {
                if (filemtime($pdfFilePath) > filemtime($swfFilePath)) return true;
            }
        }
        return false;
    }

    public function getSize($doc, $page, $mode)
    {
        $output = array();
        try {
            if ($this->getConfig('splitmode')) {
                $swfdoc    = $doc . "_" . $page . ".swf";
            } else {
                $swfdoc = $doc . ".swf";
            }
            $swfFilePath = $this->getConfig('path.swf') . $swfdoc;
            // check for directory traversal & access to non pdf files and absurdely long params
            if (!$this->validSwfParams($swfFilePath, $swfdoc, $page) ) {
                return;
            }

            if ($mode == 'width') {
                $command = $this->getConfig('cmd.query.swfwidth');
            }

            if ($mode == 'height') {
                $command = $this->getConfig('cmd.query.swfheight');
            }

            $command = str_replace("{path.swf}", $this->getConfig('path.swf'), $command);
            $command = str_replace("{swffile}", $swfFilePath, $command);
            $return_var = 0;
            //转换start
            exec($command, $output, $return_var);
            if ($return_var == 0) {
                return $this->strip_non_numerics($this->arrayToString($output));
            } else {
                return "[Error Extracting]";
            }
        } catch (Exception $ex) {
            return $ex;
        }
    }
    public function setConfig($configArr)
    {
        foreach ($configArr as $key => $value) {
            $this->config[$key] = $value;
        }
    }
    //数组转换字符串
    public function arrayToString($result_array)
    {
        reset($result_array);
        $s = "";
        $itemNo = 0;
        $total = count($result_array);
        while ($array_cell = each($result_array)) {
            $itemNo++;
            if ($itemNo < 30) {
                $s .= $array_cell['value'] . chr(10);
            } else if ($itemNo > $total - 30) {
                $s .= $array_cell['value'] . chr(10);
            } else if ($itemNo == 30) {
                $s .= chr(10) . "... ... ... ... ... ... ... ... ... ..." . chr(10) . chr(10);
            }
        }
        return $s;
    }

    public function validSwfParams($path,$doc,$page)
    {
        return !(strlen($doc) > 255 || strlen($page) > 255);
    }

    public function validPdfParams($path,$doc,$page)
    {
        return !(strlen($doc) > 255 || strlen($page) > 255);
    }

    public function getForkCommandStart()
    {
        return "";
    }

    public function getForkCommandEnd()
    {
        return " >/dev/null 2>&1 &";
    }

    public function getStringHashCode($string)
    {
      $hash = 0;
      $stringLength = strlen($string);
      for ($i = 0; $i < $stringLength; $i++) {
        $hash = 31 * $hash + $string[$i];
      }
      return $hash;
    }

    public function setCacheHeaders()
    {
        header("Cache-Control: private, max-age=10800, pre-check=10800");
        header("Pragma: private");
        header("Expires: " . date(DATE_RFC822, strtotime(" 2 day")));
    }

    public function endOrRespond()
    {
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
          header('Last-Modified: '.$_SERVER['HTTP_IF_MODIFIED_SINCE'], true, 304);
          return false;
        } else {
            return true;
        }
    }

    public function strip_non_numerics($string)
    {
        return preg_replace('/\D/', '', $string);
    }
}