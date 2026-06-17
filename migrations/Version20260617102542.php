<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260617102542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE oauth2_auth_code (identifier VARCHAR(255) NOT NULL, revoked BOOLEAN NOT NULL, expiry_date_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, client_id VARCHAR(80) NOT NULL, PRIMARY KEY (identifier))');
        $this->addSql('CREATE INDEX IDX_1D2905B519EB6921 ON oauth2_auth_code (client_id)');
        $this->addSql('ALTER TABLE oauth2_auth_code ADD CONSTRAINT FK_1D2905B519EB6921 FOREIGN KEY (client_id) REFERENCES oauth2_client (identifier) NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oauth2_auth_code DROP CONSTRAINT FK_1D2905B519EB6921');
        $this->addSql('DROP TABLE oauth2_auth_code');
    }
}
