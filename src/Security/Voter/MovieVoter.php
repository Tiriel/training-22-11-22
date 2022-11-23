<?php

namespace App\Security\Voter;

use App\Entity\Movie;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class MovieVoter extends Voter
{
    public const VIEW = 'movie.view';
    public const EDIT = 'movie.edit';

    public function __construct(
        private AuthorizationCheckerInterface $checker
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof Movie;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if ($this->checker->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $user = $token->getUser();

        return match ($attribute) {
            self::VIEW => $this->checkView($subject, $user),
            self::EDIT => $this->checkEdit($subject, $user),
            default => false,
        };
    }

    private function checkView(Movie $movie, ?UserInterface $user = null): bool
    {
        $age = !$user instanceof User
            ?: $user->getBirthday()->diff(new \DateTimeImmutable())->y;

        return match ($movie->getRated()) {
            'G', 'Not Rated' => true,
            'PG', 'PG-13' => \is_int($age) && $age >= 13,
            'R', 'NC-17' => \is_int($age) && $age >= 17,
            default => false,
        };
    }

    private function checkEdit(Movie $movie, ?UserInterface $user = null): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        return $this->checkView($movie, $user) && $user === $movie->getCreatedBy();
    }
}