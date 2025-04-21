<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250408083105 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE livres ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE livres ADD CONSTRAINT FK_927187A412469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('CREATE INDEX IDX_927187A412469DE2 ON livres (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livres DROP FOREIGN KEY FK_927187A412469DE2');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP INDEX IDX_927187A412469DE2 ON livres');
        $this->addSql('ALTER TABLE livres DROP category_id');
    }
}
