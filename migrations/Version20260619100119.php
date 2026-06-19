<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260619100119 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE oauth2_access_token DROP CONSTRAINT fk_454d967319eb6921');
        $this->addSql('DROP INDEX idx_454d967319eb6921');
        $this->addSql('ALTER TABLE oauth2_auth_code DROP CONSTRAINT fk_1d2905b519eb6921');
        $this->addSql('DROP INDEX idx_1d2905b519eb6921');
        $this->addSql('ALTER TABLE oauth2_client DROP CONSTRAINT fk_669ff9c9a76ed395');
        $this->addSql('DROP TABLE oauth2_client');
        $this->addSql('ALTER TABLE oauth2_access_token ADD user_identifier VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE oauth2_access_token DROP client_id');
        $this->addSql('ALTER TABLE oauth2_auth_code DROP client_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE oauth2_client (identifier VARCHAR(80) NOT NULL, secret VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, user_id INT NOT NULL, PRIMARY KEY (identifier))');
        $this->addSql('CREATE INDEX idx_669ff9c9a76ed395 ON oauth2_client (user_id)');
        $this->addSql('ALTER TABLE oauth2_client ADD CONSTRAINT fk_669ff9c9a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE oauth2_access_token ADD client_id VARCHAR(80) NOT NULL');
        $this->addSql('ALTER TABLE oauth2_access_token DROP user_identifier');
        $this->addSql('ALTER TABLE oauth2_access_token ADD CONSTRAINT fk_454d967319eb6921 FOREIGN KEY (client_id) REFERENCES oauth2_client (identifier) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_454d967319eb6921 ON oauth2_access_token (client_id)');
        $this->addSql('ALTER TABLE oauth2_auth_code ADD client_id VARCHAR(80) NOT NULL');
        $this->addSql('ALTER TABLE oauth2_auth_code ADD CONSTRAINT fk_1d2905b519eb6921 FOREIGN KEY (client_id) REFERENCES oauth2_client (identifier) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_1d2905b519eb6921 ON oauth2_auth_code (client_id)');
    }
}
