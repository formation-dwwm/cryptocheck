<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190821142919 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, image VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE accounts');
        $this->addSql('DROP TABLE sessions');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE accounts (account_id INT UNSIGNED AUTO_INCREMENT NOT NULL, account_name VARCHAR(254) NOT NULL COLLATE utf8_general_ci, account_password VARCHAR(254) NOT NULL COLLATE utf8_general_ci, account_enabled TINYINT(1) DEFAULT \'1\' NOT NULL, account_key VARCHAR(32) DEFAULT NULL COLLATE utf8_general_ci, account_expiry DATE DEFAULT \'1999-01-01\' NOT NULL, account_email VARCHAR(20) DEFAULT NULL COLLATE utf8_general_ci, account_tel VARCHAR(10) DEFAULT NULL COLLATE utf8_general_ci, UNIQUE INDEX account_name (account_name), PRIMARY KEY(account_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sessions (session_id INT UNSIGNED AUTO_INCREMENT NOT NULL, session_account_id INT UNSIGNED NOT NULL, session_cookie CHAR(32) NOT NULL COLLATE utf8_general_ci, session_start DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX session_cookie (session_cookie), PRIMARY KEY(session_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('DROP TABLE article');
    }
}
