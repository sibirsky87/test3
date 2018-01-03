
==
сomposer install
bin/console doctrine:database:create --if-not-exists
bin/console doctrine:schema:create  
bin/console server:run