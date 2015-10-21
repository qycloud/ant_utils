<?php
namespace Utils\Pdf2swf;
/**
 * pdf转换配置类
 *
 * @package   swftool
 * @author    Mtao <jmtao33@gmail.com>
 * @copyright Copyright (C) 2012 Safirst Technology (www.a-y.com.cn)
 */
abstract class Base
{

    public $config = array(
        "test_pdf2swf"                   => 1,
        "test_pdf2json"                  => "",
        "allowcache"                     => 0,
        "splitmode"                      => 0,
        "path.pdf"                       => '',
        "path.swf"                       => '',
        "renderingorder.primary"         => "flash",
        "renderingorder.secondary"       => "html",
        "cmd.conversion.singledoc"       => '',
        "cmd.conversion.splitpages"      => '',
        "cmd.conversion.renderpage"      => '',
        "cmd.conversion.rendersplitpage" => '',
        "cmd.conversion.jsonfile"        => '',
        "cmd.searching.extracttext"      => "\"swfstrings\" \"{swffile}\"",
        "cmd.query.swfwidth"             => "swfdump \"{swffile}\" -X",
        "cmd.query.swfheight"            => "swfdump \"{swffile}\" -Y",
        "pdf2swf"                        => 1
    );

    public function __construct()
    {
        $singledoc = "\"pdf2swf\" \"{path.pdf}{pdffile}\" -o \"{path.swf}{pdffile}.swf\" "
            ."-f -T 9 -t -s storeallcharacters -s linknameurl -s languagedir='/usr/local/xpdf'";
        $splitpages = "\"pdf2swf\" \"{path.pdf}{pdffile}\" -o \"{path.swf}{pdffile}_%.swf\" "
            ."-f -T 9 -t -s storeallcharacters -s linknameurl -s languagedir='/usr/local/xpdf'";
        $renderpage = "\"swfrender\" \"{path.swf}{swffile}\" -p {page} -o "
            ."\"{path.swf}{pdffile}_{page}.png\" -X 1024 -s keepaspectratio";
        $rendersplitpage = "\"pdf2json\" \"{path.pdf}{pdffile}\" -enc UTF-8 -compress \"{path.swf}{jsonfile}\"";
        $jsonfile = "\"pdf2json\" \"{path.pdf}{pdffile}\" -enc UTF-8 -compress \"{path.swf}{jsonfile}\"";

        $this->config['cmd.conversion.singledoc'] = $singledoc;
        $this->config['cmd.conversion.splitpages'] = $splitpages;
        $this->config['cmd.conversion.renderpage'] = $renderpage;
        $this->config['cmd.conversion.rendersplitpage'] = $rendersplitpage;
        $this->config['cmd.conversion.jsonfile'] = $jsonfile;
    }

    public function getConfigs()
    {
        return $this->config;
    }

    public function getConfig($key = null)
    {
        if ($key !== null) {
            if (isset($this->config[$key])) {
                return $this->config[$key];
            } else {
                return null;
            }
        } else {
            return $this->config;
        }
       $df = "\"pdf2swf\" \"{path.pdf}{pdffile}\" -o \"{path.swf}{pdffile}.swf\""
        ." -f -T 9 -t -s storeallcharacters -s linknameurl";
    }

    public function getDocUrl()
    {
        return "swftool Error";
    }

}
