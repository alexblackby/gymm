<?php

namespace AppBundle\Admin;

use AppBundle\Service\ThumbnailService;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UserAdmin extends AbstractAdmin
{
    /**
     * @var ThumbnailService
     */
    private $thumbnailService;

    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        ThumbnailService $thumbnailService
    ) {
        parent::__construct($code, $class, $baseControllerName);
        $this->thumbnailService = $thumbnailService;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $user = $this->getSubject();

        $fileFieldOptions = ['required' => false];
        if ($user) {
            $webPath = $this->thumbnailService->getWebPath($user);
            if ($webPath) {
                $fileFieldOptions['help'] = '<img src="' . $webPath . '" class="admin-preview" />';
            }
        }

        $formMapper
            ->add('email', EmailType::class)
            ->add('avatar', 'file', $fileFieldOptions);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('email');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('email');
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
        $collection->remove('delete');
    }
}

