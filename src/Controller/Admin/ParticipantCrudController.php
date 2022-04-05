<?php

namespace App\Controller\Admin;

use App\Entity\Participant;
use Doctrine\DBAL\Types\JsonType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ParticipantCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {

        return Participant::class;
    }


//    public function configureFields(string $pageName): iterable
//    {
//        return [
//
//        ];
//    }

}
