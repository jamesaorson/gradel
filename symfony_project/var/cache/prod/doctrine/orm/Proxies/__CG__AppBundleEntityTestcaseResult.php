<?php

namespace Proxies\__CG__\AppBundle\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class TestcaseResult extends \AppBundle\Entity\TestcaseResult implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = ['submission' => NULL, 'testcase' => NULL, 'is_correct' => NULL, 'execution_time' => NULL];



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {
        unset($this->submission, $this->testcase, $this->is_correct, $this->execution_time);

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }

    /**
     * 
     * @param string $name
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->__getLazyProperties())) {
            $this->__initializer__ && $this->__initializer__->__invoke($this, '__get', [$name]);

            return $this->$name;
        }

        trigger_error(sprintf('Undefined property: %s::$%s', __CLASS__, $name), E_USER_NOTICE);
    }

    /**
     * 
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->__getLazyProperties())) {
            $this->__initializer__ && $this->__initializer__->__invoke($this, '__set', [$name, $value]);

            $this->$name = $value;

            return;
        }

        $this->$name = $value;
    }

    /**
     * 
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        if (array_key_exists($name, $this->__getLazyProperties())) {
            $this->__initializer__ && $this->__initializer__->__invoke($this, '__isset', [$name]);

            return isset($this->$name);
        }

        return false;
    }

    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return ['__isInitialized__', 'id', 'submission', 'testcase', 'is_correct', 'execution_time'];
        }

        return ['__isInitialized__', 'id'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (TestcaseResult $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

            unset($this->submission, $this->testcase, $this->is_correct, $this->execution_time);
        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', []);
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', []);
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function __construct4($sub, $test, $correct, $time)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, '__construct4', [$sub, $test, $correct, $time]);

        return parent::__construct4($sub, $test, $correct, $time);
    }

    /**
     * {@inheritDoc}
     */
    public function setSubmission($sub)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setSubmission', [$sub]);

        return parent::setSubmission($sub);
    }

    /**
     * {@inheritDoc}
     */
    public function setTestcase($tc)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTestcase', [$tc]);

        return parent::setTestcase($tc);
    }

    /**
     * {@inheritDoc}
     */
    public function setIsCorrect($correct)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIsCorrect', [$correct]);

        return parent::setIsCorrect($correct);
    }

    /**
     * {@inheritDoc}
     */
    public function setExecutionTime($time)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setExecutionTime', [$time]);

        return parent::setExecutionTime($time);
    }

    /**
     * {@inheritDoc}
     */
    public function getSubmission()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSubmission', []);

        return parent::getSubmission();
    }

    /**
     * {@inheritDoc}
     */
    public function getTestcase()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTestcase', []);

        return parent::getTestcase();
    }

    /**
     * {@inheritDoc}
     */
    public function getIsCorrect()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIsCorrect', []);

        return parent::getIsCorrect();
    }

    /**
     * {@inheritDoc}
     */
    public function getExecutionTime()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getExecutionTime', []);

        return parent::getExecutionTime();
    }

}
