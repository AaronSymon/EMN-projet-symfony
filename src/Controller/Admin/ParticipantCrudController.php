<?php

namespace App\Controller\Admin;

use App\Entity\Participant;
use App\Entity\Site;
use App\Form\RegistrationFormType;
use App\Form\SiteType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\MenuItemTrait;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\PasswordHasherAdapter;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


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
            TextField::new('password')->setFormType(PasswordType::class)->hideOnIndex(),
            ImageField::new('photo')
                ->setBasePath(' uploads/')
                ->setUploadDir('public/uploads')
                ->setFormType(FileUploadType::class)
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
            AssociationField::new('siteRatache'),
            BooleanField::new('administrateur')->setDefaultColumns(0),
            BooleanField::new('actif')->hideOnIndex(),
        ];
    }






}
