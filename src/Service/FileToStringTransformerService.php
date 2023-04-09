<?php

namespace App\Service;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileToStringTransformerService implements DataTransformerInterface
{
    public function transform($file)
    {
        if ($file === null) {
            return '';
        }
        return $file->getFilename();
    }
    public function reverseTransform($filename)
    {
        if ($filename instanceof UploadedFile) {
            return null;
        }
        return new File($filename->getPathname());
    }
}
