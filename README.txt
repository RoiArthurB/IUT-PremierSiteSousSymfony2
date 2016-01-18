#suppresion de la base
php app/console doctrine:database:drop --force;
#creation de la base
php app/console doctrine:database:create;
#verification des entités
php app/console doctrine:schema:validate;
#mise à jour de la base
php app/console doctrine:schema:update --force;

#creation d'un admin login admin, password pass
php app/console fos:user:create admin test@example.com pass;
php app/console fos:user:promote admin ROLE_ADMIN
