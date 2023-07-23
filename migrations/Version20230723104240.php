<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230723104240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Добавили отношения один ко многим от пользователя к транзакциям';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction ADD user_account_id INT NOT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D13C0C9956 FOREIGN KEY (user_account_id) REFERENCES user_account (id)');
        $this->addSql('CREATE INDEX IDX_723705D13C0C9956 ON transaction (user_account_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D13C0C9956');
        $this->addSql('DROP INDEX IDX_723705D13C0C9956 ON transaction');
        $this->addSql('ALTER TABLE transaction DROP user_account_id');
    }
}
