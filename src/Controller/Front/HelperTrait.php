<?php

namespace App\Controller\Front;



trait HelperTrait
{
    /**
     * Permet de générer un Slug
     * à partir d'un string.
     * @param string $text
     * @return string
     */
    public function slugify(string $text): string
    {
        return Transliterator::transliterate($text);
    }
}