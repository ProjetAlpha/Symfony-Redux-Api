<?php

if (file_exists('./vendor/bin/php-cs-fixer')) {
    echo "*************** phpcsfixer target=src *******************\n";
    exec("sh vendor\bin\php-cs-fixer fix ./src/ --rules=@PSR2 2>&1");
    echo "*************** phpcsfixer target=tests *****************\n";
    exec("sh vendor\bin\php-cs-fixer fix ./tests/ --rules=@PSR2 2>&1");
}

if (file_exists('./vendor/bin/phpcbf')) {
    echo "*************** phpcbf target=src ***********************\n";
    exec("sh vendor\bin\phpcbf src 2>&1");
    echo "*************** phpcbf target=tests *********************\n";
    exec("sh vendor\bin\phpcbf tests 2>&1");
}
