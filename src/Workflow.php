<?php
namespace Neverdane\Crudity;

use Neverdane\Crudity\Form\Form;
use Neverdane\Crudity\Form\Request;
use Neverdane\Crudity\Form\RequestManager;
use Neverdane\Crudity\Form\Response;

class Workflow
{
    const EVENT_VALIDATION_BEFORE = "preValidation";
    const EVENT_VALIDATION_AFTER = "postValidation";
    const EVENT_FILTER_BEFORE = "preFilter";
    const EVENT_FILTER_AFTER = "postFilter";
    const EVENT_PROCESS_BEFORE = "preProcess";
    const EVENT_PROCESS_AFTER = "postProcess";
    const EVENT_CREATE_BEFORE = "preCreate";
    const EVENT_CREATE_AFTER = "postCreate";
    const EVENT_UPDATE_BEFORE = "preUpdate";
    const EVENT_UPDATE_AFTER = "postUpdate";
    const EVENT_DELETE_BEFORE = "preDelete";
    const EVENT_DELETE_AFTER = "postDelete";
    const EVENT_READ_BEFORE = "preRead";
    const EVENT_READ_AFTER = "postRead";
    const EVENT_SEND_BEFORE = "preSend";

    /**
     * @var Form|null
     */
    private $form = null;
    /**
     * @var null|RequestManager
     */
    private $requestManager;

    /**
     * @param Form $form
     */
    public function __construct($form)
    {
        $this->form = $form;
    }

    private function validate()
    {
        if ($this->form->isWorkflowOpened()) {
            Error::initialize();
            $this->notify(self::EVENT_VALIDATION_BEFORE);
            $this->form->getRequestManager()->validate();
            if (Response::STATUS_ERROR === $this->form->getRequestManager()->getResponse()->getStatus()) {
                $this->form->closeWorkflow();
            }
            $this->notify(self::EVENT_VALIDATION_AFTER);
        }
    }

    private function filter()
    {
        if ($this->form->isWorkflowOpened()) {
            $this->notify(self::EVENT_FILTER_BEFORE);
            $this->form->getRequestManager()->filter();
            $this->notify(self::EVENT_FILTER_AFTER);
        }
    }

    private function process($type)
    {
        if ($this->form->isWorkflowOpened()) {
            $this->notify(self::EVENT_PROCESS_BEFORE);
            switch ($type) {
                case Request::ACTION_CREATE :
                    $this->create();
                    break;
                case Request::ACTION_UPDATE :
                    $this->update();
                    break;
            }
            $this->notify(self::EVENT_PROCESS_AFTER);
        }
    }

    private function create()
    {
        if ($this->form->isWorkflowOpened()) {
            $this->notify(self::EVENT_CREATE_BEFORE);
            $this->form->getRequestManager()->create();
            $this->notify(self::EVENT_CREATE_AFTER);
        }
    }

    private function update()
    {
        if ($this->form->isWorkflowOpened()) {
            $this->notify(self::EVENT_UPDATE_BEFORE);
            $this->form->update();
            $this->notify(self::EVENT_UPDATE_AFTER);
        }
    }

    private function delete()
    {
        if ($this->form->isWorkflowOpened()) {
            $this->notify(self::EVENT_DELETE_BEFORE);
            $this->form->delete();
            $this->notify(self::EVENT_DELETE_AFTER);
        }
    }

    private function read()
    {
        if ($this->form->isWorkflowOpened()) {
            $this->notify(self::EVENT_READ_BEFORE);
            $this->form->read();
            $this->notify(self::EVENT_READ_AFTER);
        }
    }

    private function send()
    {
        $this->notify(self::EVENT_SEND_BEFORE);
        $this->form->getRequestManager()->getResponse()->send();
    }

    private function notify($event)
    {
        $observers = $this->form->getObservers();
        foreach ($observers as $observer) {
            $observer->$event($this->form);
        }

    }

    public function start()
    {
        $action = $this->form->getRequestManager()->getRequest()->getAction();
        switch ($action) {
            default:
                $this->form->getRequestManager()->getRequest()->setAction(Request::ACTION_CUSTOM);
            case Request::ACTION_CUSTOM :
                $this->validate();
                $this->filter();
                break;
            case Request::ACTION_CREATE :
            case Request::ACTION_UPDATE :
                $this->validate();
                $this->filter();
                $this->process($action);
                break;
            case Request::ACTION_DELETE :
                $this->delete();
                break;
            case Request::ACTION_READ :
                $this->read();
                break;
        }
        $this->send();
        return $this;
    }

}
