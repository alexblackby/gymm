<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Muscle;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MuscleAdmin extends AbstractAdmin
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($code, $class, $baseControllerName);
        $this->entityManager = $entityManager;
    }

    public function getNewInstance()
    {
        $entity = parent::getNewInstance();

        $parent = $this->getEntityParent();
        if ($parent) {
            $entity->setParent($parent);
        }

        return $entity;
    }

    private function getEntityParent()
    {
        $parent = null;
        $parentId = $this->getEntityParentId();
        if ($parentId) {
            $parent = $this->entityManager->getReference(Muscle::class, $parentId);
        }
        return $parent;
    }

    public function generateUrl(
        $name,
        array $parameters = [],
        $absolute = \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH
    ) {
        $parentId = $this->getEntityParentId();
        if ($parentId && in_array($name, ['create', 'edit', 'delete'])) {
            $parameters["parent_id"] = $parentId;
        }

        return parent::generateUrl($name, $parameters, $absolute);
    }

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $parent = $this->getEntityParent();
        if ($parent) {
            $query->andWhere(
                $query->expr()->eq($query->getRootAliases()[0] . '.parent', ':parent')
            );
            $query->setParameter('parent', $parent);
        } else {
            $query->andWhere(
                $query->expr()->isNull($query->getRootAliases()[0] . '.parent')
            );
        }
        return $query;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', TextType::class)
            ->add('parent', EntityType::class, [
                'class' => Muscle::class,
                'choice_label' => 'title',
                'required' => false
            ])
            ->add('description', TextareaType::class, ['required' => false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('parent');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);

        if ($this->getEntityParentId()) {
            $listMapper->add('title', 'string', ['label' => 'Name']);
        } else {
            $listMapper->add('Group', 'string', array('template' => 'admin/muscle/children_link.html.twig'));
        }


        $listMapper->add('_action', null, [
            'actions' => [
                'edit' => [],
                'delete' => [],
            ]
        ]);
    }

    private function getEntityParentId()
    {
        $parentId = null;
        if ($this->hasRequest()) {
            $parentId = intval($this->request->query->get("parent_id"));
        }
        return $parentId;
    }
}

