<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231201171016 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chambre ADD is_disponible TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE reservation CHANGE checking_at checking_at DATETIME NOT NULL, CHANGE checkout_at checkout_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chambre DROP is_disponible');
        $this->addSql('ALTER TABLE reservation CHANGE checking_at checking_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE checkout_at checkout_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
