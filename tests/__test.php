<?php

require_once '../autoload.php';

use PhpObjects\TypeCast\DataType;
use PhpObjects\TypeCast\DataCastStrategy;

use PhpObjects\TypeCast\Strategy\ToString;
use PhpObjects\TypeCast\Strategy\Type\ToString\Json;
use PhpObjects\TypeCast\Strategy\Type\ToString\Generic;

use PhpObjects\TypeCast\Strategy\ToArray;
use PhpObjects\TypeCast\Strategy\Type\ToArray\FromJson;
use PhpObjects\TypeCast\Strategy\Type\ToArray\FromGenericString;

use PhpObjects\TypeCast\Strategy\Mapper\ToCaseMapper;
use PhpObjects\TypeCast\Strategy\Mapper\Type\ToCase\UpperCase;

use PhpObjects\TypeCast\Strategy\Mapper\ToSpecialCharacterMapper;

use PhpObjects\TypeCast\Strategy\Mapper\ToEncodingMapper;
use PhpObjects\TypeCast\Strategy\Mapper\Type\ToEncoding\ISO88591;


$data = [
    'Name' => 'Bica',
    'Vorname' => 'Andreas',
    'Strasse' => 'Spinnereiinsel',
    'HausNr' => '3A',
    'PLZ' => '83059',
    'Ort' => 'Kolbermoor',
    'Land' => 'DE',
    'Free' => 'ä,ö,ü,Ü,Ä,Ö,ß',
];



$strategy = (new DataCastStrategy)
    ->addStrategy((new ToString)->setFormat((new Json))
        ->addMapper((new ToSpecialCharacterMapper))
        ->addMapper((new ToEncodingMapper)->setEncoding((new ISO88591)))
    )
;
$jsonTypeCast = (new DataType)->cast($data, $strategy);

PhpObjects\Core\Base::dump($strategy);
PhpObjects\Core\Base::dump($data);
PhpObjects\Core\Base::dump($jsonTypeCast);



//$strategy = (new DataCastStrategy)
//    #->addStrategy((new ToString)->setFormat((new Generic)->setKeyFormat('%s:')->setValueFormat('%s')->setElementDelimiter('|')))
//    #->addStrategy((new ToString)->setFormat((new Generic)))
//    ->addStrategy((new ToString)->setFormat((new Json)))
//;
//$dataTypeCast = (new DataType)->cast($data, $strategy);
//
//PhpObjects\Core\Base::dump($strategy);
//PhpObjects\Core\Base::dump($dataTypeCast);



$strategy = (new DataCastStrategy)
    ->addStrategy((new ToArray)->fromFormat((new FromJson)))
    #->addStrategy((new ToArray)) // Auto Type Detection
;
$dataTypeCast = (new DataType)->cast($jsonTypeCast, $strategy);

PhpObjects\Core\Base::dump('From json to array:');
PhpObjects\Core\Base::dump($strategy);
PhpObjects\Core\Base::dump($dataTypeCast);



$strategy = (new DataCastStrategy)
    ->addStrategy((new ToString)->setFormat((new Generic)->setKeyFormat('%s:')->setValueFormat('%s')->setElementDelimiter('|')))
;
$dataTypeCast = (new DataType)->cast($data, $strategy);

PhpObjects\Core\Base::dump($strategy);
PhpObjects\Core\Base::dump($dataTypeCast);



$strategy = (new DataCastStrategy)
    ->addStrategy((new ToArray)->fromFormat((new FromGenericString)->setElementDelimiter('|')->setKeyValueDelimiter(':')))
;
$dataTypeCast = (new DataType)->cast($dataTypeCast, $strategy);

PhpObjects\Core\Base::dump('From generic string to array:');
PhpObjects\Core\Base::dump($strategy);
PhpObjects\Core\Base::dump($dataTypeCast);


