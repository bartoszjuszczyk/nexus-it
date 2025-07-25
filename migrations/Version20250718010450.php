<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250718010450 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ticket_rating (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, ticket_id INT NOT NULL, rate INT NOT NULL, source_ip VARCHAR(255) NOT NULL, INDEX IDX_A2CB4CE9F675F31B (author_id), INDEX IDX_A2CB4CE9700047D2 (ticket_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ticket_rating ADD CONSTRAINT FK_A2CB4CE9F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket_rating ADD CONSTRAINT FK_A2CB4CE9700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket_rating DROP FOREIGN KEY FK_A2CB4CE9F675F31B');
        $this->addSql('ALTER TABLE ticket_rating DROP FOREIGN KEY FK_A2CB4CE9700047D2');
        $this->addSql('DROP TABLE ticket_rating');
    }
}
