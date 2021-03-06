<?php
/**
 * @license MIT
 * @link    https://github.com/UFOMelkor/DkplusCrud canonical source repository
 */

namespace DkplusCrud\Controller\Action;

use DkplusCrud\Controller\Feature\FeatureInterface as Feature;
use DkplusCrud\Controller\Controller;
use DkplusCrud\Controller\Event;
use Zend\EventManager\EventManagerInterface as EventManager;

/**
 * @author Oskar Bley <oskar@programming-php.net>
 * @since  0.1.0
 */
abstract class AbstractAction implements ActionInterface
{
    /**
     * Correlates to the of the name of the method in ActionControllers.
     * E.g. update, read, …
     *
     * @var string
     */
    protected $name;

    /** @var Controller */
    protected $controller;

    /** @var Feature[] */
    protected $features = array();

    /** @var EventManager */
    protected $events;

    /** @var Event */
    private $event;

    /**
     * @param string $name Correlates to the name of the method in ActionControllers.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /** @return string */
    public function getName()
    {
        return $this->name;
    }

    public function addFeature(Feature $feature)
    {
        $this->features[] = $feature;
    }

    public function attachTo(EventManager $events)
    {
        $this->events = $events;
        foreach ($this->features as $feature) {
            $feature->attachTo($this->getName(), $events);
        }
    }

    public function setController(Controller $controller)
    {
        $this->controller = $controller;
    }

    /**
     * @param string $prefix Typically an emtpy string, pre, post or notFound
     */
    protected function triggerEvent($prefix)
    {
        $eventName = $prefix == ''
                   ? $this->getName()
                   : $prefix . \ucFirst($this->getName());

        $this->events->trigger($eventName, $this->getEvent());
    }

    /**
     * @return Event
     * @throws Exception\RuntimeException if event and controller have not been set.
     */
    public function getEvent()
    {
        if (!$this->event) {

            if (!$this->controller) {
                throw new Exception\RuntimeException(
                    'Could not provide a default event because no controller has been injected'
                );
            }

            $this->event = new Event($this->controller);
        }

        return $this->event;
    }

    public function setEvent(Event $event)
    {
        $this->event = $event;
    }
}
