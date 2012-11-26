<?php
/**
 * @category   Dkplus
 * @package    Crud
 * @subpackage Controller\Feature
 * @author     Oskar Bley <oskar@programming-php.net>
 */

namespace DkplusCrud\Controller\Feature;

use RuntimeException;
use Zend\EventManager\EventInterface as Event;

/**
 * @category   Dkplus
 * @package    Crud
 * @subpackage Controller\Feature
 * @author     Oskar Bley <oskar@programming-php.net>
 */
class AjaxFormSupport extends AbstractFeature
{
    /** @var string */
    protected $eventType = self::EVENT_TYPE_POST;

    public function execute(Event $event)
    {
        $ctrl = $this->getController();
        $dsl  = $event->getParam('result');

        if ($dsl === null) {
            throw new RuntimeException('missing dsl');
        }

        return $dsl->onAjaxRequest($ctrl->dsl()->assign()->formMessages()->asJson());
    }
}