<?php

namespace App\Test;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\Custumer;
use Doctrine\ORM\EntityManagerInterface;

class CustomApiTestCase extends ApiTestCase
{
    protected function createCustumer(string $email, string $plainPassword): Custumer
    {
        $custumer = new Custumer();

        $encoder = self::$container->get('security.password_encoder');
        $password = $encoder->encodePassword($custumer, $plainPassword);
        $name = substr($email, 0, strpos($email, '@'));

        $custumer->setEmail($email);
        $custumer->setPassword($password);
        $custumer->setName($name);
        $custumer->setFullname($name);
        $custumer->setCreatedDate(new \DateTime());
        $custumer->setUpdatedDate(new \DateTime());

        $entityManager = $this->getEntityManager();
        $entityManager->persist($custumer);
        $entityManager->flush();

        return $custumer;
    }

    protected function login(Client $client, string $email, string $plainPassword)
    {
        $client->request('POST', '/login', [
            'json' => [
                'email' => $email,
                'password' => $plainPassword
            ],
        ]);
    }

    protected function createCustumerAndLogin(Client $client, string $email, string $plainPassword): Custumer
    {
        $custumer = $this->createCustumer($email, $plainPassword);

        $this->login($client, $email, $plainPassword);

        return $custumer;
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return self::$container->get('doctrine')->getManager();
    }
}
