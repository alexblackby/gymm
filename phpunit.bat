@IF [%1]==[--skip-setup] GOTO RUN
php bin/console doctrine:schema:update --force --env=test
php bin/console doctrine:fixtures:load --no-interaction --env=test
:RUN
@IF [%1]==[--no-test] GOTO END
vendor/bin/phpunit
:END
