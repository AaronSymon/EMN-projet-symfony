<?php

namespace App\Controller\Admin;

use App\Entity\Participant;
use App\Entity\Site;
use App\Form\RegistrationFormType;
use App\Form\SiteType;
use Doctrine\DBAL\Types\IntegerType;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
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
            TextField::new('password')->setFormType(RegistrationFormType::class),
            AssociationField::new('siteRatache'),
            BooleanField::new('administrateur')->setDefaultColumns(0),
            BooleanField::new('actif')->hideOnIndex(),
            TextField::new('password')->setFormType(PasswordType::class)

        ];
    }






}
