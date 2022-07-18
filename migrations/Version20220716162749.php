<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220716162749 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, nom VARCHAR(100) NOT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_3AF34668727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pages (id INT AUTO_INCREMENT NOT NULL, categories_id INT NOT NULL, widgets_id INT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, author INT NOT NULL, date DATETIME NOT NULL, status INT NOT NULL, images VARCHAR(255) DEFAULT NULL, INDEX IDX_2074E575A21214B7 (categories_id), INDEX IDX_2074E575A98ED6F4 (widgets_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE widgets (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, nom VARCHAR(100) NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_9D58E4C1727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE pages ADD CONSTRAINT FK_2074E575A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE pages ADD CONSTRAINT FK_2074E575A98ED6F4 FOREIGN KEY (widgets_id) REFERENCES widgets (id)');
        $this->addSql('ALTER TABLE widgets ADD CONSTRAINT FK_9D58E4C1727ACA70 FOREIGN KEY (parent_id) REFERENCES widgets (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF34668727ACA70');
        $this->addSql('ALTER TABLE pages DROP FOREIGN KEY FK_2074E575A21214B7');
        $this->addSql('ALTER TABLE pages DROP FOREIGN KEY FK_2074E575A98ED6F4');
        $this->addSql('ALTER TABLE widgets DROP FOREIGN KEY FK_9D58E4C1727ACA70');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE pages');
        $this->addSql('DROP TABLE widgets');
    }
}
