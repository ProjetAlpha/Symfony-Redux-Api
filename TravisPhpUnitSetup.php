<?php 

if (file_exists('phpunit.xml.dist')) {
    $xml = new DOMDocument('1.0', 'utf-8');
    $xml->formatOutput = true; 
    $xml->preserveWhiteSpace = false;
    $xml->load('phpunit.xml.dist');

    //Get server tags.
    $server = $xml->getElementsByTagName('server');

    // Replace environment value for travis tests.
    if (!findAndReplace($server, ["test", "dev", "prod"])) {
        echo "############# TRAVIS TEST ENVIRONMENT SETUP - FAIL #############";
        exit ("Parsing error : unexpected APP_ENV value. Support 'test', 'dev' and 'prod'.");
    }
    
    // TODO: create a specific env for travis tests.
    // 1 - create .env.travis (copy .env.test)
    // 2 - create config package/travis (copy config/package/test + set bundle.php)

    $xml->save('phpunit.xml.dist');
    echo "############# TRAVIS TEST ENVIRONMENT SETUP - SUCESS #############";
} else {
    exit('File phpunit.xml.dist not found.');
}

function findAndReplace($xml, $valueList) {
    
    foreach($xml as $a) {

        $attributesCount = $a->attributes->length;
        for ($i = 0; $i < $attributesCount; $i++)
        {
            $attribut = $a->attributes->item($i);

            if ($attribut->name !== "value") continue;

            if ($attribut->value == "travis") return true;

            if (in_array($attribut->value, $valueList)) {
                
                $attribut->value = "travis";
                return true;
            }
        }
    }

    return false;
}