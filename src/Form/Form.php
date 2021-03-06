<?php

/*
 * This file is part of the Crudity package.
 *
 * (c) Alban Pommeret <alban@aocreation.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Neverdane\Crudity\Form;

use Neverdane\Crudity\AbstractObserver;
use Neverdane\Crudity\Db;

/**
 * @package Neverdane\Crudity
 * @author Alban Pommeret <alban@aocreation.com>
 */
class Form
{
    /**
     * @var string
     * The id that identifies the Form in the Registry
     */
    private $id;
    /**
     * @var array
     * The observers added to the Form in order to affect it during the workflow
     */
    private $observers = array();
    /**
     * @var View
     * Instance of the view used for the rendering
     */
    private $view = null;
    /**
     * @var array
     * An array of customized errors for some fields or validators on this Form
     */
    private $errorMessages = array();
    /**
     * @var string
     * The adapter key matching the dbLayerAdapter instance we want to use on this Form
     */
    private $dbAdapterKey = null;
    /**
     * @var Db\Entity[] array
     */
    private $entities = array();

    /**
     * @param null|Config $config
     * We can pass a Config object in order to customize this Form
     */
    public function __construct($config = null)
    {
        // If no config was given, we instantiate a default one
        $this->config = $config ?: new Config();
    }

    /**
     * Sets the id of the Form
     * This id is the key used to store the Form in the Registry
     * @param string $id
     * @return Form
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Gets the id of the Form
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Adds an Observer to the Form that let us listen to events during the workflow process
     * @param AbstractObserver $observer
     * @return Form
     */
    public function addObserver($observer)
    {
        $this->observers[] = $observer;
        return $this;
    }

    /**
     * Sets an array of Observers
     * @param array $observers
     * @return Form
     */
    public function setObservers($observers)
    {
        $this->observers = $observers;
        return $this;
    }

    /**
     * Returns all the Observers set on the Form
     * @return array
     */
    public function getObservers()
    {
        return $this->observers;
    }

    /**
     * Sets a View to the Form that will handle the rendering
     * @param View $view
     * @return $this
     */
    public function setView($view)
    {
        $this->view = $view;
        // The View needs some Config that is stored in the global Form Config so we retrieve and affect it
        $this->view->setConfig($this->config->getConfig(Config::TYPE_VIEW));
        return $this;
    }

    /**
     * Returns the View set on the Form
     * @return View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Sets an array of custom Error Messages overriding the default ones.
     * The array must be formatted as below :
     *
     *      array(
     *          "Fields" => array(
     *              "{Field Name}" => array(
     *                  {ERROR CODE} => "{Your message}"
     *              )
     *          ),
     *          "Validators" => array(
     *              "{Validator Name}" => array(
     *                  {ERROR CODE} => "{Your message}"
     *              )
     *          )
     *      );
     *
     * @param array $errorMessages
     * @return  $this
     */
    public function setErrorMessages($errorMessages)
    {
        $this->errorMessages = $errorMessages;
        return $this;
    }

    /**
     * Returns the customized Error Messages set on the Form
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }

    /**
     * Returns the Config Object set on the Form
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Sets the adapter key that will be used to retrieve the DbAdapter registered in the Db
     * @param string $key
     * @return Form
     */
    public function setDbAdapterKey($key)
    {
        $this->dbAdapterKey = $key;
        return $this;
    }

    /**
     * Returns the DbAdapter registered in the Db in the Db from the dbAdapterKey set on the Form
     * The adapter is not directly set on the Form because it embeds the connection that must not be stored in session
     * If no dbAdapterKey was set on the Form, the method will return the default dbAdapter registered on Db if any
     * @return Db\Layer\AdapterInterface|null
     */
    public function getDbAdapter()
    {
        return Db\Db::retrieveAdapter($this->dbAdapterKey);
    }

    /**
     * @param array $entities
     * @return $this
     */
    public function setEntities($entities)
    {
        /** @var Db\Entity $entity */
        foreach ($entities as $entity) {
            $this->entities[$entity->getName()] = $entity;
        }
        return $this;
    }

    /**
     * @param string $entityName
     * @return null|Db\Entity
     */
    public function getEntity($entityName = 'default')
    {
        return isset($this->entities[$entityName])
            ? $this->entities[$entityName]
            : null;
    }

    /**
     * @return Db\Entity[]
     */
    public function getEntities()
    {
        return $this->entities;
    }

}
