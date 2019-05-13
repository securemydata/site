<?php
/**
 * Created by PhpStorm.
 * User: guillaume.regairaz
 * Date: 03/05/2019
 * Time: 11:15
 */
// src/AppBundle/Security/User/WebserviceUserProvider.php
namespace AppBundle\Security\User;

use AppBundle\Security\User\WebserviceUser;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;


class WebserviceUserProvider implements UserProviderInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function loadUserByUsername($username)
    {
        return $this->fetchUser($username);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof WebserviceUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        $username = $user->getUsername();

        return $this->fetchUser($username);
    }

    public function supportsClass($class)
    {
        return WebserviceUser::class === $class;
    }

    private function fetchUser($username)
    {
        // make a call to your webservice here
        $user = '';
        // pretend it returns an array on success, false if there is no user
        $user = $this->container->get('doctrine')->getManager()
            ->getRepository(User::class)
            ->findOneByEmail($username);

        echo $username."========================";
        $password   = $user->getPassphrase();
        //$username   = $user->getFirstName().' '.$user->getLastName();
        $salt       = $user->getEmail();
        $roles      = ['ROLE_ADMIN'];



            return new WebserviceUser($username, $password, $salt, $roles);

        throw new UsernameNotFoundException(
            sprintf('Username "%s" does not exist.', $username)
        );
    }
}
