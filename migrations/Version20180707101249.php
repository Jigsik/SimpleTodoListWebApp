<?php declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180707101249 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, todo_list_id INT NOT NULL, name VARCHAR(255) NOT NULL, created DATETIME NOT NULL, finished TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_527EDB25E8A7DCFA (todo_list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE todo_list (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25E8A7DCFA FOREIGN KEY (todo_list_id) REFERENCES todo_list (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25E8A7DCFA');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE todo_list');
    }
}
