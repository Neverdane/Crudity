<?php
/**
 * Created by PhpStorm.
 * User: Alban
 * Date: 20/06/14
 * Time: 23:21
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../vendor/autoload.php';

use Neverdane\Crudity\Crudity;

Crudity::setConfig("config.json");
Crudity::listen();
$form = Crudity::createFromFile("form.php"); //Add observers on form creation !
echo $form->getView()->render();
