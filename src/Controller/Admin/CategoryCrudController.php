<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

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
            yield ImageField::new('image')
            ->setBasePath("images/categories")
            ->setUploadDir("public/images/categories")
            ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]');
    }    
}
