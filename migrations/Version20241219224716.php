<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241219224716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE price_history_services ADD id INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('CREATE UNIQUE INDEX unique_service_date ON price_history_services (service_id, effective_date)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE price_history_services MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX unique_service_date ON price_history_services');
        $this->addSql('DROP INDEX `PRIMARY` ON price_history_services');
        $this->addSql('ALTER TABLE price_history_services DROP id');
        $this->addSql('ALTER TABLE price_history_services ADD PRIMARY KEY (service_id, effective_date)');
    }
}
