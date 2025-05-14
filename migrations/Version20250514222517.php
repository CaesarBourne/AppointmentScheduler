<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250514222517 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE appointment DROP email
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participant DROP INDEX UNIQ_D79F6B11E7927C73, ADD UNIQUE INDEX UNIQ_D79F6B11E7927C74 (email)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_D79F6B11E7927C72 ON participant
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE appointment ADD email VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_D79F6B11E7927C72 ON participant (email)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE participant RENAME INDEX uniq_d79f6b11e7927c74 TO UNIQ_D79F6B11E7927C73
        SQL);
    }
}
