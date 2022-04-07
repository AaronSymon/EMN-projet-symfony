<?php

namespace App\Controller\Admin;

use App\Entity\Participant;
use App\Entity\Site;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;


class ParticipantCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Participant::class ;
    }
    public static function getEntityTest(): string
    {
        return Site::class ;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('pseudo'),
            TextField::new('nom'),
            TextField::new('prenom'),
            EmailField::new('email'),
            TextField::new('telephone'),
            TextField::new('password')->setFormType(PasswordType::class)->hideOnIndex(),
            ImageField::new('photo')
                ->setBasePath(' uploads/')
                ->setUploadDir('public/uploads')
                ->setFormType(FileUploadType::class)
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
            AssociationField::new('siteRatache'),
            BooleanField::new('administrateur')->setDefaultColumns(0),
            BooleanField::new('actif'),

        ];
    }
    public function configureActions(Actions $actions): Actions
    {
        $disable = Action::new('disable', 'désactiver')->linkToCrudAction('disableUser');

        return$actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $disable)
//            ->disable(Action::DELETE, Action::EDIT)
            ;
    }

    public function disableUser(AdminContext $context)
    {
        $user = $context->getEntity()->getInstance();

//        $user->setIsActive(false);

        $em = $this->getDoctrine()->getManager();

        $em->persist($user);

        $em->flush();

        echo'<pre>'.print_r(get_class_methods($this), true) .'</pre>';

        $this->addFlash('success', 'message');

        return $this->redirectToRoute('admin', [], '301');
    }
}



