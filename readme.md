Syslovo sklad
=================

Požadavky
------------
- PHP 8.1.

Instalace
------------

- composer install
- vytvořte prázdné adresáře log, temp a tempForTests
- nakonfigurujte DB a v /config/.env a v /config/database_connection.neon, nebo předejte parametry pomocí ENV
- zmigrujte pomocí php bin/console.php migrations:migrate nebo požijte db_dump.sql obsahující testovací data

Spuštení webu
----------------
- spuštění PHP: php -S localhost:8000 -t www
- web je pak přístupný na: http://localhost:8000
