<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Pub;
use App\Entity\UserToUser;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

class CurrentUserExtension implements QueryCollectionExtensionInterface
{
    public function __construct(
        private Security $security
    ) {}


    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $user = $this->security->getUser();
        // if role_admin return (include)
        if ($resourceClass === Pub::class) {
            if (in_array("ROLE_ADMIN", $user->getRoles())) {
                return;
            }

            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->andWhere(sprintf('%s.owner = :current_user', $rootAlias));
            $queryBuilder->setParameter('current_user', $user->getId());
        }
    }

}
