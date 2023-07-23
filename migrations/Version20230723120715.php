<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230723120715 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Добавления данных о возможных сервисах';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO service_info (name, price) VALUES ('вывоз мусора', 30),('электричество',20),('лифт', 40)");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
