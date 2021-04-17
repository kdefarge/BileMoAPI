<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210417152326 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE command (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, status ENUM(\'En attente\', \'Validé\', \'En cours\', \'Terminé\'), created_date DATETIME NOT NULL, updated_date DATETIME NOT NULL, INDEX IDX_8ECAEAD4A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE custumer (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, fullname VARCHAR(255) NOT NULL, created_date DATETIME NOT NULL, updated_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_BE9D39F2E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mobile (id INT AUTO_INCREMENT NOT NULL, model_name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, price INT NOT NULL, stock INT NOT NULL, created_date DATETIME NOT NULL, updated_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mobile_command (mobile_id INT NOT NULL, command_id INT NOT NULL, INDEX IDX_6E602721B806424B (mobile_id), INDEX IDX_6E60272133E1689A (command_id), PRIMARY KEY(mobile_id, command_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, custumer_id INT NOT NULL, email VARCHAR(180) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, created_date DATETIME NOT NULL, updated_date DATETIME NOT NULL, INDEX IDX_8D93D649503B0073 (custumer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE mobile_command ADD CONSTRAINT FK_6E602721B806424B FOREIGN KEY (mobile_id) REFERENCES mobile (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mobile_command ADD CONSTRAINT FK_6E60272133E1689A FOREIGN KEY (command_id) REFERENCES command (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649503B0073 FOREIGN KEY (custumer_id) REFERENCES custumer (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mobile_command DROP FOREIGN KEY FK_6E60272133E1689A');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649503B0073');
        $this->addSql('ALTER TABLE mobile_command DROP FOREIGN KEY FK_6E602721B806424B');
        $this->addSql('ALTER TABLE command DROP FOREIGN KEY FK_8ECAEAD4A76ED395');
        $this->addSql('DROP TABLE command');
        $this->addSql('DROP TABLE custumer');
        $this->addSql('DROP TABLE mobile');
        $this->addSql('DROP TABLE mobile_command');
        $this->addSql('DROP TABLE user');
    }
}
