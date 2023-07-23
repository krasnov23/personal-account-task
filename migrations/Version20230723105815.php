<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230723105815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Добавили многие ко многим от пользователя к сервисам';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_account_service_info (user_account_id INT NOT NULL, service_info_id INT NOT NULL, INDEX IDX_223340823C0C9956 (user_account_id), INDEX IDX_22334082920F9364 (service_info_id), PRIMARY KEY(user_account_id, service_info_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_account_service_info ADD CONSTRAINT FK_223340823C0C9956 FOREIGN KEY (user_account_id) REFERENCES user_account (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_account_service_info ADD CONSTRAINT FK_22334082920F9364 FOREIGN KEY (service_info_id) REFERENCES service_info (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_account_service_info DROP FOREIGN KEY FK_223340823C0C9956');
        $this->addSql('ALTER TABLE user_account_service_info DROP FOREIGN KEY FK_22334082920F9364');
        $this->addSql('DROP TABLE user_account_service_info');
    }
}
