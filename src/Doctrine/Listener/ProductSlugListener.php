<?php

namespace App\Doctrine\Listener;

use App\Entity\Product;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductSlugListener
{
    protected $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(Product $entity, LifecycleEventArgs $event)
    {
        if (empty($entity->getSlug())) {
            // SluggerInterface
            $entity->setSlug(strtolower($this->slugger->slug($entity->getName())));
        }
    }
}
