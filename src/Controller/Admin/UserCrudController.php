<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        yield ImageField::new('avatarImage')
            ->setBasePath("images/profiles")
            ->setUploadDir("public/images/profiles")
            ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]');
        yield IdField::new('id')
            ->hideOnForm();
        yield EmailField::new('email');
        yield TextField::new('password')->onlyOnForms();
        yield BooleanField::new('isVerified')
            ->renderAsSwitch(false);
        $roles = ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_MODERATOR', 'ROLE_USER'];
        yield ArrayField::new('roles')
            ->hideOnForm();
        yield ChoiceField::new('roles')
            // ->setHelp('Available roles: ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_MODERATOR, ROLE_USER')
            ->setFormTypeOptions([
                'choices' => array_combine($roles, $roles),
                'multiple' => true,
                'expanded' => true
            ])
            ->onlyOnForms();
        yield TextField::new('name');
        yield TextField::new('surname');
        yield TextField::new('mobTel');
        yield TextField::new('city');
        yield TextField::new('codeZIP');
        yield TextField::new('street');
        yield TextField::new('buildNum');

        yield TextEditorField::new('additionalInfo');
        yield DateTimeField::new('created_at')
            ->hideOnForm(); /* similar to ->onlyOnIndex() */
    }
}
