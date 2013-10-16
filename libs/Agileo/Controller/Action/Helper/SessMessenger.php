<?php
/**
 * @author     Mirosław Kapinos (mkapinos@agileo.pl)
 * @copyright  Copyright (c) 2005-2013 Agileo.pl Mirosław Kapinos (http://www.agileo.pl)
 * @license    MIT license
 */

class Agileo_Controller_Action_Helper_SessMessenger extends Zend_Controller_Action_Helper_Abstract implements IteratorAggregate, Countable
{

    static protected $_messages = array();
    static protected $_session = null;

    public function __construct()
    {
        if (!self::$_session instanceof Zend_Session_Namespace) {
            self::$_session = new Zend_Session_Namespace($this->getName());
            if (!empty(self::$_session->messages) && is_array(self::$_session->messages)) {
                self::$_messages = self::$_session->messages;
            }
            self::$_session->messages = array();
        }
    }

    public function preDispatch()
    {
        $view = $this->getActionController()->view;
        $view->sessMessengerMessages = $this->hasMessages() ? $this->getMessages() : array();
        return $this;
    }

    public function postDispatch()
    {
        return $this;
    }

    public function addMessage($message, $type = 'notice')
    {
        self::$_session->messages[] = array(
            $message,
            $type
        );
        return $this;
    }

    public function error($message)
    {
        return $this->addMessage($message, 'error');
    }

    public function notice($message)
    {
        return $this->addMessage($message, 'notice');
    }

    public function correct($message)
    {
        return $this->addMessage($message, 'correct');
    }

    public function hasMessages()
    {
        return isset(self::$_messages);
    }

    public function getMessages()
    {
        if ($this->hasMessages()) {
            return self::$_messages;
        }

        return array();
    }

    public function clearMessages()
    {
        if ($this->hasMessages()) {
            self::$_messages = array();
            return true;
        }

        return false;
    }

    public function hasCurrentMessages()
    {
        return isset(self::$_session->messages);
    }

    public function getCurrentMessages()
    {
        if ($this->hasCurrentMessages()) {
            return self::$_session->messages;
        }

        return array();
    }

    public function clearCurrentMessages()
    {
        if ($this->hasCurrentMessages()) {
            unset(self::$_session->messages);
            return true;
        }

        return false;
    }

    public function getIterator()
    {
        if ($this->hasMessages()) {
            return new ArrayObject($this->getMessages());
        }

        return new ArrayObject();
    }

    public function count()
    {
        if ($this->hasMessages()) {
            return count($this->getMessages());
        }

        return 0;
    }

    public function direct($message)
    {
        return $this->addMessage($message);
    }

}
