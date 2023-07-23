<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230723065512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создание сущностей пользователя, сервисов и транзакций';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE service_info (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, price INT NOT NULL, amount INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, service_name VARCHAR(255) NOT NULL, total_price INT NOT NULL, date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', account_balance INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_account (id INT AUTO_INCREMENT NOT NULL, balance INT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE service_info');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE user_account');
    }
}
