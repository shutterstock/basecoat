<?php

namespace Basecoat;

/**
* Provides messages processing functionality
*
* @author Brent Baisley <brent@bigstockphoto.com>
*/
class Messages
{
    /**
    * Template file to use for message output
    */
    protected $tpl_file = null;

    /**
    * Create an instance of the Message class
    *
    * @return Object instance of Message class
    */
    public function __construct() {}

    /**
    * Set the template file to use for message output
    *
    * @param String $tpl_file path to the template file to load
    */
    public function setTemplate($tpl_file)
    {
        $this->tpl_file = $tpl_file;
    }

    /**
    * Add an information type message to output
    */
    public function info($message)
    {
        $this->add('info', $message);
    }

    /**
    * Add an warning type message to output
    */
    public function warn($message)
    {
        $this->add('warn', $message);
    }

    /**
    * Add an error type message to output
    */
    public function error($message)
    {
        $this->add('error', $message);
    }

    /**
    * Get currently loaded messages
    * Optionally filter on message type
    *
    * @param String $msg_type message type to return (info, warn, error)
    * @return Array list of currently loaded messages
    */
    public function get($msg_type=null)
    {
        if ( is_null($msg_type) ) {
            if ( isset($_SESSION['messages'][$msg_type]) ) {
                return $_SESSION['messages'][$msg_type];
            } else {
                return array();
            }
        } else {
            return $_SESSION['messages'];
        }
    }

    /**
    * Add a message of the specified type
    *
    * Messages are stored in the SESSION so they persist
    * until they are outputted to the page
    *
    * @param String $type message type to add
    * @param String $message message to add
    * @return Integer current number of messages of the specified type
    */
    protected function add($type, $message)
    {
        if ( !isset($_SESSION['messages']) ) {
            $_SESSION['messages']   = array();
        }
        $type = strtolower($type);
        if ( isset($_SESSION['messages'][$type]) ) {
            $_SESSION['messages'][$type][] = $message;
        } else {
            $_SESSION['messages'][$type] = array($message);
        }
        return count($_SESSION['messages'][$type]);
    }

    /**
    * Add current messages to the page for output
    *
    * @param Boolean $clear clear messages after added to output
    * @return Integer number of messages added to output
    */
    public function display($view, $clear=true)
    {
        if ( !isset($_SESSION['messages']) ) {
            return 0;
        }
        $msg_count = 0;
        foreach($_SESSION['messages'] as $msgs) {
            $msg_count +=count($msgs);
        }
        if ( $msg_count>0 ) {
            $content = new View();
            $content->enable_data_tags = false;
            $content->multiadd($_SESSION['messages'], 'msg_');
            $msg_out = $content->processTemplate($this->tpl_file);
            $content->addToView($view);
            if ( $clear ) {
                $this->clear();
            }
            unset($content);
            return $msg_count;
        } else {
            return 0;
        }
    }

    /**
    * Clear all currently loaded messages
    *
    * @param String $msg_type optionally clear only messages of specified type (default is to clear all)
    * @return Integer number of messages cleared
    */
    public function clear($msg_type=null)
    {
        if ( !isset($_SESSION['messages']) ) {
            return;
        }
        if ( is_null($msg_type) ) {
            unset($_SESSION['messages']);
        } elseif ( isset($_SESSION['messages'][$msg_type]) ) {
            unset($_SESSION['messages'][$msg_type]);
        }
    }

}
