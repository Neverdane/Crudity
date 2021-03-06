<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// We get the Composer autoloader
require '../../vendor/autoload.php';
// We create a namespace alias to Crudity
use Neverdane\Crudity;

// We store our database connection to Crudity Db. This connection will be used on database manipulation.
$pdo = new PDO('mysql:host=192.168.1.21;dbname=crudity', 'root', '');
// We register this connection as 'pdo' in order to pass the wanted connection to our Forms
Crudity\Db\Db::registerAdapter('pdo', new Crudity\Db\Layer\PdoAdapter($pdo));

// We instantiate a Registry, it will store the Crudity Forms instances we will create
$registry = new Crudity\Registry();
// We listen to requests. This method handles the core Crudity part.
Crudity\Crudity::listen();

// We get the Form instance from the parsed given HTML file
// We also declare that the entity we want to use is "users"
$form = Crudity\Crudity::createFromFile('form.php', 'users');
// We register the form instance in the registry:
$registry->storeForm($form);

?>
<html>
    <head>
        <?php // Crudity comes with unobtrusive style, let's add it ?>
        <link rel="stylesheet" href="/public/crudity/css/crudity.css">
    </head>
    <body>
        <?php // We render the form ?>
        <?php echo $form->getView()->render(); ?>
        <?php // Crudity is based on jQuery, we have to require it ?>
        <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
        <?php // We add the Crudity js core file ?>
        <script src="/public/crudity/js/crudity.js"></script>
        <script>
            $().ready(function () {
                <?php // We trigger Crudity on our form and set its action to creation ?>
                $('#userForm').crudity({action: 'create'});
            });
        </script>
    </body>
</html>
