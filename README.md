# training_Symfony2-8_Angular1.5_AdminLTE

For Symfony: <http://symfony.com/>
For Angularjs: <https://angularjs.org/>
For AdminLTE: <https://almsaeedstudio.com>
Knowledge sources:
* https://thinkster.io/angularjs-es6-tutorial
* https://knpuniversity.com/screencast/symfony-rest4

# HOW TO DEV

## FRONTEND

### DEV

cd front-root;
npm install;
gulp;

### DEPLOY

cd front-root;
npm install;
gulp deploy;


## BACKEND

cd symfony-root;
composer install;
php app/console doctrine:schema:update --force;
php app/console doctrine:fixtures:load;
php app/console server:start;
