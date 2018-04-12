<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180412094846 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE station DROP FOREIGN KEY FK_9F39F8B18BAC62AF');
        $this->addSql('ALTER TABLE station ADD CONSTRAINT FK_9F39F8B18BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE station DROP FOREIGN KEY FK_9F39F8B18BAC62AF');
        $this->addSql('ALTER TABLE station ADD CONSTRAINT FK_9F39F8B18BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
    }
}
