<?php

namespace App\Components\User\Business;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

interface UserBusinessFacadeInterface
{
    public function getUserEntity(UserInterface $user): ?User;
}
