<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Service\FileToStringTransformerService;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        
            yield TextField::new('name');
            yield TextEditorField::new('description');
            yield Field::new('image')
            ->setFormType(FileType::class)
            ->setFormTypeOptions([
                'required' => false,
                'attr' => ['accept' => 'image/*'],
                'data_class' => null
            ])
            ->onlyOnForms();
    }
    
}
