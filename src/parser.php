<?php

namespace PhpProject48\Parser;

$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';

if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} else {
    require_once $autoloadPath2;
}


use Funct\Collection;
use Funct\Strings;

use function Funct\Collection\sortBy;

//преобразование из json в php формат
function parsing($fileContent)
{
    $jsonInPhp = file_get_contents($fileContent);//получаем содержимое файла из пути
    $data = json_decode($jsonInPhp); //преобразование Json строки в php значение
    $keys = (get_object_vars($data)); //возвращает ассоц. массив нестатических свойств обьекта
    return $keys;
}

function toDiffFormat($diff)
{
    $json = Collection\toJson($diff);
    $deletSymb = Strings\strip($json, '"', "{", "}");
    $adColon = str_replace(':', ": ", $deletSymb);
    $res = explode(',', $adColon);
    $res2 = implode(PHP_EOL, $res) . PHP_EOL;
    return "{\n" . $res2 . "}\n";
}

function genDiff($firstFile, $secondFile)
{
    $dataFirstFile = array_map(fn($key, $value) => ['  - ', $key, $value], array_keys(array_diff_assoc($firstFile, $secondFile)), array_diff_assoc($firstFile, $secondFile));//данные есть только в 1м файле, нужно поставить -
    $dataSecondFile = array_map(fn($key, $value) => ['  + ', $key, $value], array_keys(array_diff_assoc($secondFile, $firstFile)), array_diff_assoc($secondFile, $firstFile)); //данные есть только во 2м файле, нужно поставить +
    $dataInAllFiles =  array_map(fn($key, $value) => ['    ', $key, $value], array_keys(array_intersect_assoc($firstFile, $secondFile)), array_intersect_assoc($firstFile, $secondFile)); //данные присутствуют в обоих файлах, ничего не ставим
    $dataColl = array_merge($dataFirstFile, $dataSecondFile, $dataInAllFiles);
    $ABCSort = Collection\sortBy($dataColl, fn($a) => $a[1]);
    $res = array_reduce($ABCSort, fn($acc, $op) => array_merge($acc, [$op[0] . $op[1] => $op[2]]), []);
    return toDiffFormat($res);
}
