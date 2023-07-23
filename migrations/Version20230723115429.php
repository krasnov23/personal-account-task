<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230723115429 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Добавление данных пользователя';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO user_account (id, balance, name) VALUES (1,500,'Maksim')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
