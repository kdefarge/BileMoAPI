<?php

namespace App\Doctrine;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class UserListingSetOwnerListener
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function prePersist(User $user)
    {
        if ($user->getCustumer()) {
            return;
        }

        if ($this->security->getUser()) {
            $user->setCustumer($this->security->getUser());
        }
    }
}
