<?php
namespace Utils\PinYin;

class PinYin
{
    private static $pinYin;

    public static function conv(
        string $chineses, int $type = 1, bool $space = false, bool $keepmark = false, bool $keepletter = true
    ): string
    {
        if (!isset(self::$pinYin)) {
            self::$pinYin = include __DIR__.'/Data.php';
        }
        
        if (!$keepletter) {
            $chineses = preg_replace('/[a-z]+/', '', $chineses);
        }
        $data = '';
        $length = mb_strlen($chineses);
        for ($i = 0; $i < $length; $i ++) {
            $chinese = mb_substr($chineses, $i, 1);
            if (preg_match('/[a-zA-Z0-9\- ]/', $chinese)) {
                $data .= $chinese;
            } else {
                $pinYin = self::arraySearch($chinese);
                if ($keepmark) {
                    $data .= self::setSym($type, $chinese, $pinYin);
                } else if ($pinYin) {
                    $data .= self::setSym($type, $chinese, $pinYin);
                }
            }
        }
        $data = strtolower($data);
        if (!$space) {
            $data = preg_replace('/\s/', '', $data);
        }
        return $data;
    }

    private static function setSym(int $type, string $chinese, string $pinYin): string
    {
        switch ($type) {
            case 1:
                return ($pinYin ? $pinYin : $chinese) . ' ';
            case 2:
                return $chinese . ($pinYin ? $pinYin : '') . ' ';
            case 3:
                return ($pinYin ? $pinYin : '') . $chinese . ' ';
        }
        return '';
    }

    private static function arraySearch(string $chinese): string
    {
        foreach (self::$pinYin as $key => $value) {
            if (strpos($value, $chinese) !== false) {
                return $key;
            }
        }
        return '';
    }
}