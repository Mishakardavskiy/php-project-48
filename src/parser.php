<?php

namespace PhpProject48\Parser;

function parsing($fileContent)
{
    $jsonInPhp = file_get_contents($fileContent);
    $data = json_decode($jsonInPhp);
    $keys = (get_object_vars($data));
    return $keys;
}

