<?php
/**
 * @license MIT
 * @link    https://github.com/UFOMelkor/DkplusCrud canonical source repository
 */

namespace DkplusCrud\Controller\Feature;

use DkplusCrud\Controller\Event;
use DkplusCrud\Service\ServiceInterface as Service;
use Zend\Http\Response;

/**
 * Handles form data, validation and success handling.
 *
 * This feature does several things. First it uses postRedirectGet for getting the form data.
 * Then it puts the form as <code>form<code>-variable into the view model and if there are form data
 * available from postRedirectGet, they will be applied to the form.
 * Last if the form is valid it saves the form data using the service.
 * By default this feature will not handle ajax requests. So if you want to do this,
 * you must explicit enable it by calling <code>handleAjaxRequest()</code>.
 *
 * @author Oskar Bley <oskar@programming-php.net>
 * @since  0.1.0
 */
class FormHandling extends AbstractFeature
{
    /** @var Service */
    protected $service;

    /** @var boolean */
    protected $handleAjaxRequest = false;

    public function __construct(Service $service)
    {
        $this->service  = $service;
    }

    /** @return void */
    public function handleAjaxRequest()
    {
        $this->handleAjaxRequest = true;
    }

    public function execute(Event $event)
    {
        if (!$event->getRequest()->isXmlHttpRequest()
            || $this->handleAjaxRequest
        ) {

            $controller = $event->getController();
            $prg        = $controller->postRedirectGet();

            if ($prg instanceof Response) {
                $event->setResponse($prg);
                $event->stopPropagation();
                return;
            }

            $form = $event->getForm();
            $event->getViewModel()->setVariable('form', $form);

            if (!\is_array($prg)) {
                return;
            }

            $form->setData($prg);

            if ($form->isValid()) {

                $entity = $event->hasIdentifier()
                        ? $this->service->update($form->getData(), $event->getIdentifier())
                        : $this->service->create($form->getData());
                $event->setEntity($entity);
            }
        }
    }
}
