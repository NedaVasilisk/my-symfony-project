<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241224155646 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add auto-increment id column to price_history_parts and make it the primary key.';
    }

    public function up(Schema $schema): void
    {
        // 1. Удаляем текущий составной первичный ключ
        $this->addSql('ALTER TABLE price_history_parts DROP PRIMARY KEY');

        // 2. Добавляем новое автоинкрементное поле id
        $this->addSql('ALTER TABLE price_history_parts ADD id INT AUTO_INCREMENT PRIMARY KEY');

        // 3. Добавляем индексы для part_id и effective_date (если нужны)
        $this->addSql('CREATE INDEX IDX_PART_ID ON price_history_parts (part_id)');
        $this->addSql('CREATE INDEX IDX_EFFECTIVE_DATE ON price_history_parts (effective_date)');
    }

    public function down(Schema $schema): void
    {
        // Откатываем изменения

        // 1. Удаляем новый первичный ключ
        $this->addSql('ALTER TABLE price_history_parts DROP PRIMARY KEY');

        // 2. Удаляем поле id
        $this->addSql('ALTER TABLE price_history_parts DROP COLUMN id');

        // 3. Восстанавливаем составной первичный ключ
        $this->addSql('ALTER TABLE price_history_parts ADD PRIMARY KEY (part_id, effective_date)');
    }
}
