<?php

namespace App\Test;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\Command;
use App\Entity\Custumer;
use App\Entity\Mobile;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

        $this->updateEntity($custumer);

        return $custumer;
    }

    protected function createAdmin(string $email, string $plainPassword): Custumer
    {
        $custumer = $this->createCustumer($email, $plainPassword);
        $custumer->setRoles(['ROLE_ADMIN']);

        $this->updateEntity($custumer);

        return $custumer;
    }

    protected function createMobile(string $modelName, string $description = '', $price = 0, $stock = 0): Mobile
    {
        $mobile = new Mobile();

        $mobile->setModelName($modelName);
        $mobile->setDescription($description);
        $mobile->setPrice($price);
        $mobile->setStock($stock);

        $this->updateEntity($mobile);

        return $mobile;
    }

    protected function createUser(Custumer $custumer, string $email, string $firstname = '', string $lastname = ''): User
    {
        $user = new User();

        $user->setCustumer($custumer);
        $user->setEmail($email);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);

        $this->updateEntity($user);

        return $user;
    }

    protected function createCommand(User $user, array $mobiles, string $status = Command::STATUS_WAITING): Command
    {
        $command = new Command();

        $command->setUser($user);
        foreach ($mobiles as $mobile) {
            $command->addMobile($mobile);
        }
        $command->setStatus($status);

        $this->updateEntity($command);

        return $command;
    }

    protected function updateEntity($entity)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush();
    }

    protected function removeEntity($entity)
    {
        $repository = $this->getRepository(get_class($entity));
        $entity = $repository->find($entity->getId());
        $entityManager = $this->getEntityManager();
        $entityManager->remove($entity);
        $entityManager->flush();
    }

    protected function getRepository($entityClass): ServiceEntityRepository
    {
        return $this->getEntityManager()->getRepository($entityClass);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return self::$container->get('doctrine')->getManager();
    }

    protected function retrieveToken(Client $client, string $email, string $plainPassword)
    {
        $response = $client->request('POST', '/authentication_token', [
            'json' => [
                'email' =>  $email,
                'password' => $plainPassword,
            ],
        ]);

        return $response->toArray()['token'];
    }

    protected function retrieveTokenFixtures(Client $client, string $userName): string
    {
        return $this->retrieveToken($client, $userName . '@example.org', 'toctoc');
    }
}
