<?php
namespace App\EventSubscriber;

use App\Entity\BlogPost;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class EasyAdminSubscriber implements EventSubscriberInterface
{
private $slugger;
private $security;

public function __construct(SluggerInterface $slugger , Security $security)
{
$this->slugger = $slugger;
$this->security = $security;

}

public static function getSubscribedEvents()
{
return [
BeforeEntityPersistedEvent::class => ['setBlogPostSlugAndUser'],
];
}

public function setBlogPostSlugAndUser(BeforeEntityPersistedEvent $event)
{
$entity = $event->getEntityInstance();

if (!($entity instanceof BlogPost)) {
return;
}

$slug = $this->slugger->slugify($entity->getTitle());
$entity->setSlug($slug);

$now = new DateTime('now');
$entity->setCreatedAt($now);
$user =$this->security->getUser();
$entity->setUser($user);
}
}