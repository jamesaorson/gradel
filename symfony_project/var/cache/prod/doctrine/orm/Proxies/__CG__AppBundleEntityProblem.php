<?php

namespace Proxies\__CG__\AppBundle\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Problem extends \AppBundle\Entity\Problem implements \Doctrine\ORM\Proxy\Proxy
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
    public static $lazyPropertiesDefaults = ['testcases' => NULL, 'assignment' => NULL, 'name' => NULL, 'description' => NULL, 'instructions' => NULL, 'language' => NULL, 'default_code' => NULL, 'weight' => NULL, 'gradingmethod' => NULL, 'attempts_allowed' => NULL, 'time_limit' => NULL, 'is_extra_credit' => NULL];



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {
        unset($this->testcases, $this->assignment, $this->name, $this->description, $this->instructions, $this->language, $this->default_code, $this->weight, $this->gradingmethod, $this->attempts_allowed, $this->time_limit, $this->is_extra_credit);

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
            return ['__isInitialized__', 'id', 'testcases', 'assignment', 'name', 'description', 'instructions', 'language', 'default_code', 'weight', 'gradingmethod', 'attempts_allowed', 'time_limit', 'is_extra_credit'];
        }

        return ['__isInitialized__', 'id'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Problem $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

            unset($this->testcases, $this->assignment, $this->name, $this->description, $this->instructions, $this->language, $this->default_code, $this->weight, $this->gradingmethod, $this->attempts_allowed, $this->time_limit, $this->is_extra_credit);
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
    public function __construct11($assign, $nm, $desc, $inst, $lang, $default, $wght, $grdmeth, $attempts, $limit, $credit)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, '__construct11', [$assign, $nm, $desc, $inst, $lang, $default, $wght, $grdmeth, $attempts, $limit, $credit]);

        return parent::__construct11($assign, $nm, $desc, $inst, $lang, $default, $wght, $grdmeth, $attempts, $limit, $credit);
    }

    /**
     * {@inheritDoc}
     */
    public function setAssignment($assign)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAssignment', [$assign]);

        return parent::setAssignment($assign);
    }

    /**
     * {@inheritDoc}
     */
    public function setName($nm)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setName', [$nm]);

        return parent::setName($nm);
    }

    /**
     * {@inheritDoc}
     */
    public function setDescription($desc)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDescription', [$desc]);

        return parent::setDescription($desc);
    }

    /**
     * {@inheritDoc}
     */
    public function setInstructions($ins)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setInstructions', [$ins]);

        return parent::setInstructions($ins);
    }

    /**
     * {@inheritDoc}
     */
    public function setLanguage($lang)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLanguage', [$lang]);

        return parent::setLanguage($lang);
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultCode($code)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDefaultCode', [$code]);

        return parent::setDefaultCode($code);
    }

    /**
     * {@inheritDoc}
     */
    public function setWeight($wght)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setWeight', [$wght]);

        return parent::setWeight($wght);
    }

    /**
     * {@inheritDoc}
     */
    public function setGradingmethod($grade)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setGradingmethod', [$grade]);

        return parent::setGradingmethod($grade);
    }

    /**
     * {@inheritDoc}
     */
    public function setAttemptAllowed($att)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAttemptAllowed', [$att]);

        return parent::setAttemptAllowed($att);
    }

    /**
     * {@inheritDoc}
     */
    public function setTimeLimit($time)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTimeLimit', [$time]);

        return parent::setTimeLimit($time);
    }

    /**
     * {@inheritDoc}
     */
    public function setIsExtraCredit($extra)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIsExtraCredit', [$extra]);

        return parent::setIsExtraCredit($extra);
    }

    /**
     * {@inheritDoc}
     */
    public function getAssignment()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAssignment', []);

        return parent::getAssignment();
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getName', []);

        return parent::getName();
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDescription', []);

        return parent::getDescription();
    }

    /**
     * {@inheritDoc}
     */
    public function getInstructions()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getInstructions', []);

        return parent::getInstructions();
    }

    /**
     * {@inheritDoc}
     */
    public function getLanguage()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLanguage', []);

        return parent::getLanguage();
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultCode()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDefaultCode', []);

        return parent::getDefaultCode();
    }

    /**
     * {@inheritDoc}
     */
    public function getWeight()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getWeight', []);

        return parent::getWeight();
    }

    /**
     * {@inheritDoc}
     */
    public function getGradingmethod()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getGradingmethod', []);

        return parent::getGradingmethod();
    }

    /**
     * {@inheritDoc}
     */
    public function getAttemptsAllowed()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAttemptsAllowed', []);

        return parent::getAttemptsAllowed();
    }

    /**
     * {@inheritDoc}
     */
    public function getTimeLimit()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTimeLimit', []);

        return parent::getTimeLimit();
    }

    /**
     * {@inheritDoc}
     */
    public function getIsExtraCredit()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIsExtraCredit', []);

        return parent::getIsExtraCredit();
    }

}
