<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260529101007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE oauth2_access_token (identifier VARCHAR(255) NOT NULL, revoked BOOLEAN NOT NULL, expiry_date_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, client_id VARCHAR(80) NOT NULL, PRIMARY KEY (identifier))');
        $this->addSql('CREATE INDEX IDX_454D967319EB6921 ON oauth2_access_token (client_id)');
        $this->addSql('CREATE TABLE oauth2_client (identifier VARCHAR(80) NOT NULL, secret VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, user_id INT NOT NULL, PRIMARY KEY (identifier))');
        $this->addSql('CREATE INDEX IDX_669FF9C9A76ED395 ON oauth2_client (user_id)');
        $this->addSql('ALTER TABLE oauth2_access_token ADD CONSTRAINT FK_454D967319EB6921 FOREIGN KEY (client_id) REFERENCES oauth2_client (identifier) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE oauth2_client ADD CONSTRAINT FK_669FF9C9A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oauth2_access_token DROP CONSTRAINT FK_454D967319EB6921');
        $this->addSql('ALTER TABLE oauth2_client DROP CONSTRAINT FK_669FF9C9A76ED395');
        $this->addSql('DROP TABLE oauth2_access_token');
        $this->addSql('DROP TABLE oauth2_client');
    }
}
