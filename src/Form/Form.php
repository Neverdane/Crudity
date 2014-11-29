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

use Neverdane\Crudity\Error;
use Neverdane\Crudity\Field\AbstractField;
use Neverdane\Crudity\Field\FieldInterface;
use Neverdane\Crudity\Request\FormRequest;
use Neverdane\Crudity\Response\FormResponse;
use Neverdane\Crudity\Field\FieldManager;
use Neverdane\Crudity\Registry;
use Neverdane\Crudity\View\FormView;

/**
 * @package Neverdane\Crudity
 * @author Alban Pommeret <alban@aocreation.com>
 */
class Form
{
    const RENDER_TYPE_FILE = 1;
    const RENDER_TYPE_HTML = 2;
    const RENDER_TYPE_OBJECTS = 3;

    private $id;
    private $observers = array();
    public $renderType = self::RENDER_TYPE_OBJECTS;
    public $render = null;
    public $persisted = false;

    private $view = null;

    private $request = null;
    private $response = null;
    private $errorMessages = array();

    public function __construct($config = null)
    {
        $this->fieldManager = new FieldManager();
        $this->config = (!is_null($config)) ? $config : new Config();
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function addObserver($observer)
    {
        $this->observers[] = $observer;
        return $this->onChange();
    }

    public function setObservers($observers)
    {
        $this->observers = $observers;
        return $this->onChange();
    }

    public function getObservers()
    {
        return $this->observers;
    }

    private function onChange()
    {
        if ($this->persisted === true) {
            $this->persist();
        }
        return $this;
    }

    public function persist()
    {
        $this->persisted = true;
        Registry::store($this->id, $this);
        return $this;
    }

    public function setView($view)
    {
        $this->view = $view;
        $this->view->setConfig($this->config->getViewConfigs());
        return $this;
    }

    /**
     * @return FormView
     */
    public function getView()
    {
        return $this->view;
    }

    public function setFieldManager($fieldManager)
    {
        $this->fieldManager = $fieldManager;
        return $this;
    }

    /**
     * @return FieldManager
     */
    public function getFieldManager()
    {
        return $this->fieldManager;
    }

    public function setRequest($request)
    {
        $this->request = $request;
        $this->response = new FormResponse();
        return $this;
    }

    /**
     * @return FormRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return FormResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function validate()
    {
        $this->getResponse()->setStatus(FormResponse::STATUS_SUCCESS);
        $fields = $this->getFieldManager()->getFields();
        /** @var FieldInterface $field */
        foreach ($fields as $field) {
            $fieldStatus = $field->validate()->getStatus();
            if ($fieldStatus !== AbstractField::STATUS_SUCCESS) {
                $this->getResponse()->setStatus(FormResponse::STATUS_ERROR);
                $message = Error::getMessage(
                    $field->getErrorCode(),
                    $this->getErrorMessages(),
                    $field->getName(),
                    $field->getErrorValidatorName(),
                    $placeholders = array(
                        "value"     => $field->getValue(),
                        "fieldName" => $field->getName()
                    )
                );
                $this->getResponse()->addError($field->getErrorCode(), $message, $field->getName());
            }
        }
    }

    public function filter()
    {

    }

    /**
     * @param array $errorMessages
     * @return  $this
     */
    public function setErrorMessages($errorMessages = array())
    {
        $this->errorMessages = $errorMessages;
        return $this;
    }

    public function getErrorMessages()
    {
        return $this->errorMessages;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }
}
