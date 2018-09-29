<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Muscle;
use Sonata\AdminBundle\Controller\CRUDController as Controller;

class MuscleController extends Controller
{

    protected function redirectTo($object)
    {
        $response = parent::redirectTo($object);

        if ($object instanceof Muscle && $response === $this->redirectToList()) {
            $parent = $object->getParent();
            if ($parent) {
                $response = $this->redirect($this->admin->generateUrl('list', ['parent_id' => $parent->getId()]));
            }
        }

        return $response;
    }
}