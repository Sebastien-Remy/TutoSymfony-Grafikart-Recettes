<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250131073128 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe ADD categoty_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B1379D066842 FOREIGN KEY (categoty_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_DA88B1379D066842 ON recipe (categoty_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B1379D066842');
        $this->addSql('DROP INDEX IDX_DA88B1379D066842 ON recipe');
        $this->addSql('ALTER TABLE recipe DROP categoty_id');
    }
}
