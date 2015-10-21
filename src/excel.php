<?php
/**
 * Excel导入、导出处理文件
 */
namespace Utils;

class Excel
{
    const XLS = 'Excel5';
    const XLSX = 'Excel2007';

    protected static $_instance = null;

    protected static $_importExtendInstanceForXls = null;
    protected static $_importExtendInstanceForXlsx = null;

    protected static $_exportExtendInstance = null;

    public static function getInstance()
    {
        if (empty(self::$_instance)) {
             self::$_instance = new Excel();
        }
        return self::$_instance;
    }

    public function read($filePath, $type = 'xls')
    {
        $readerObj = null;
        $type = strtolower($type);

        \PHPExcel_Settings::setCacheStorageMethod(
            \PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip, array()
        );

        if ($type == 'xls') {
            $readerObj = $this->_getImportExtendInstanceForXls();
        } else if ($type == 'xlsx') {
            $readerObj = $this->_getImportExtendInstanceForXlsx();
        }

        if (empty($readerObj)) {
            return false;
        }

        $excelExtendObj = $readerObj->load($filePath);

        if ($excelExtendObj->getSheetCount() < 1) {
            return false;
        }

        $allData = array();
        foreach ($excelExtendObj->getAllSheets() as $sheet) {
            $sheetData = $sheet->toArray();

            $dataCount = count($sheetData);
            if ($dataCount <= 1) {
                continue;
            }

            $fields = array();
            $dataSet = array();
            foreach ($sheetData as $rowIndex => $rowData) {
                foreach ($rowData as $cellIndex => $cellValue) {
                    if ($rowIndex == 0) {
                        $fields[$cellIndex] = $cellValue;
                    } else {
                        if (!isset($dataSet[$rowIndex - 1])) {
                            $dataSet[$rowIndex - 1] = array();
                        }
                        $dataSet[$rowIndex - 1][$fields[$cellIndex]] = $rowData[$cellIndex];
                    }
                }
            }
            $title = trim($sheet->getTitle());
            $allData[$title] = $dataSet;
        }

        return $allData;
    }

    protected function _getImportExtendInstanceForXls()
    {
        if (empty(self::$_importExtendInstanceForXls)) {
            self::$_importExtendInstanceForXls = $this->_getImportExtendInstance(self::XLS);
        }
        return self::$_importExtendInstanceForXls;
    }

    protected function _getImportExtendInstanceForXlsx()
    {
        if (empty(self::$_importExtendInstanceForXlsx)) {
            self::$_importExtendInstanceForXlsx = $this->_getImportExtendInstance(self::XLSX);
        }
        return self::$_importExtendInstanceForXlsx;
    }

    protected function _getImportExtendInstance($fileType)
    {
        return \PHPExcel_IOFactory::createReader($fileType);
    }

    public function export($fileName, $data, $outType = 'xls')
    {
        if (empty($data)) {
            return false;
        }

        $exportExtendInstance = $this->_getExportExtendInstance();

        $sheetIndex = 0;
        foreach ($data as $tabTitle => $tableData) {
            $rowCount = 1;
            if ($sheetIndex !== 0) {
               $exportExtendInstance->createSheet();
            }
            $sheet = $exportExtendInstance->getSheet($sheetIndex);
            $sheet->setTitle($tabTitle);

            foreach ($tableData as $recordData) {
                $colCount = 1;
                foreach ($recordData as $value) {
                    $sheet->setCellValueExplicit(
                        $this->_getCellIndex($colCount) . $rowCount,
                        $value
                    );
                    $colCount++;
                }
                $rowCount++;
            }
            $sheetIndex++;
        }

        $objWriter = \PHPExcel_IOFactory::createWriter(
            $exportExtendInstance, 'Excel5'
        );
        ob_end_clean();
        header('Cache-Control: max-age=0');
        header('Content-Type: application/vnd.ms-excel');
        if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE")) {
            $fileName = urlencode($fileName);
        }

        header("Content-Disposition: attachment;filename={$fileName}.{$outType}");
        $objWriter->save('php://output');
        exit(0);
    }

    public function device($fileName, $fields, $records)
    {
        if (empty($fields)) {
            return false;
        }

        $exportExtendInstance = $this->_getExportExtendInstance();
        $objActSheet = $exportExtendInstance->getActiveSheet();
        $objActSheet->setTitle($fileName);

        //设置表头标题信息
        $fieldsInfo = array();
        $cellIndex = 1;
        foreach ($fields as $fieldId => $fieldTitle) {
            if ($cellIndex > 256) {
                break;
            }
            $fieldsInfo[$fieldId] = array(
                'title' => $fieldTitle,
                'index' => $this->_getCellIndex($cellIndex++)
            );
        }

        foreach ($fieldsInfo as $fieldItem) {
            $objActSheet->setCellValueExplicit(
                $fieldItem['index'] . '1', $fieldItem['title'],
                \PHPExcel_Cell_DataType::TYPE_STRING
            );
        }

        //设置数据信息
        if (!empty($records)) {
            $rowIndex = 2;
            foreach ($records as $record) {
                foreach ($fieldsInfo as $fieldId => $fieldItem) {
                    $objActSheet->setCellValueExplicit(
                        $fieldItem['index'] . $rowIndex,
                        isset($record[$fieldId])
                        ? $record[$fieldId] : '',
                        is_string($record[$fieldId])
                        ? \PHPExcel_Cell_DataType::TYPE_STRING
                        : \PHPExcel_Cell_DataType::TYPE_NUMERIC
                    );
                }
                $rowIndex++;
            }
        }

        $objWriter = \PHPExcel_IOFactory::createWriter(
            $exportExtendInstance, 'Excel5'
        );
        header('Cache-Control: max-age=0');
        header('Content-Type: application/vnd.ms-excel');
        if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE")) {
            $fileName = urlencode($fileName);
        }
        header("Content-Disposition: attachment;filename={$fileName}.xls");
        $objWriter->save('php://output');
        exit(0);
    }


    protected function _getExportExtendInstance()
    {
        if (empty(self::$_exportExtendInstance)) {
            self::$_exportExtendInstance = new \PHPExcel();
        }
        return self::$_exportExtendInstance;
    }

    protected function _getCellIndex($intIndex)
    {
        if ($intIndex > 256) {
            return false;
        }

        $baseIndex = array(
            1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'F',
            7 => 'G', 8 => 'H', 9 => 'I', 10 => 'J', 11 => 'K', 12 => 'L',
            13 => 'M',  14 => 'N', 15 => 'O', 16 => 'P', 17 => 'Q', 18 => 'R',
            19 => 'S',  20 => 'T', 21 => 'U', 22 => 'V', 23 => 'W', 24 => 'X',
            25 => 'Y',  26 => 'Z'
        );

        if (array_key_exists($intIndex, $baseIndex)) {
            return $baseIndex[$intIndex];
        }

        $cellIndex = '';
        if ($intIndex % 26 === 0 ) {
            $cellIndex = $baseIndex[floor($intIndex / 26) - 1 ] . $baseIndex[26];
        } else {
            $cellIndex = $baseIndex[floor($intIndex / 26)] . $baseIndex[$intIndex % 26];
        }
        return $cellIndex;
    }
}
