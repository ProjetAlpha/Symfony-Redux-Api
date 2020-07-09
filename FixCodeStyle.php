<?php

if (file_exists('./vendor/bin/php-cs-fixer')) {
    exec("sh vendor\bin\php-cs-fixer fix ./src/ --rules=@PSR2");
    exec("sh vendor\bin\php-cs-fixer fix ./tests/ --rules=@PSR2");
}