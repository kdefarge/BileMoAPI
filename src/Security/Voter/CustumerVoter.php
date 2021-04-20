<?php

namespace App\Security\Voter;

use App\Entity\Custumer;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CustumerVoter extends Voter
{
    const OWNER = 'OWNER';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::OWNER])) {
            return false;
        }

        if (!$subject instanceof Custumer) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $custumer = $token->getUser();

        if (!$custumer instanceof UserInterface) {
            return false;
        }

        /** @var Custumer $subject */

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::OWNER:
                return $this->isOwner($custumer, $subject);
                break;
        }

        throw new \Exception(sprintf('Unhandled attribute "%s"', $attribute));
    }

    private function isOwner(Custumer $custumer, $subject)
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        
        return $custumer === $subject;
    }
}
