<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250712190635 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ticket_event (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, ticket_id INT NOT NULL, old_status_id INT DEFAULT NULL, new_status_id INT DEFAULT NULL, assigned_worker_id INT DEFAULT NULL, attachment_id INT DEFAULT NULL, type INT NOT NULL, comment LONGTEXT DEFAULT NULL, INDEX IDX_139E692CF675F31B (author_id), INDEX IDX_139E692C700047D2 (ticket_id), INDEX IDX_139E692C2E43440C (old_status_id), INDEX IDX_139E692C596805D2 (new_status_id), INDEX IDX_139E692C12810F94 (assigned_worker_id), INDEX IDX_139E692C464E68B (attachment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ticket_event ADD CONSTRAINT FK_139E692CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket_event ADD CONSTRAINT FK_139E692C700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id)');
        $this->addSql('ALTER TABLE ticket_event ADD CONSTRAINT FK_139E692C2E43440C FOREIGN KEY (old_status_id) REFERENCES ticket_status (id)');
        $this->addSql('ALTER TABLE ticket_event ADD CONSTRAINT FK_139E692C596805D2 FOREIGN KEY (new_status_id) REFERENCES ticket_status (id)');
        $this->addSql('ALTER TABLE ticket_event ADD CONSTRAINT FK_139E692C12810F94 FOREIGN KEY (assigned_worker_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket_event ADD CONSTRAINT FK_139E692C464E68B FOREIGN KEY (attachment_id) REFERENCES ticket_attachment (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket_event DROP FOREIGN KEY FK_139E692CF675F31B');
        $this->addSql('ALTER TABLE ticket_event DROP FOREIGN KEY FK_139E692C700047D2');
        $this->addSql('ALTER TABLE ticket_event DROP FOREIGN KEY FK_139E692C2E43440C');
        $this->addSql('ALTER TABLE ticket_event DROP FOREIGN KEY FK_139E692C596805D2');
        $this->addSql('ALTER TABLE ticket_event DROP FOREIGN KEY FK_139E692C12810F94');
        $this->addSql('ALTER TABLE ticket_event DROP FOREIGN KEY FK_139E692C464E68B');
        $this->addSql('DROP TABLE ticket_event');
    }
}
