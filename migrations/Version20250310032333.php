<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250310032333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE "clothes_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "sales_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "salesValue_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "suppliers_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "clothes" (id INT NOT NULL, name VARCHAR(255) NOT NULL, size VARCHAR(255) NOT NULL, resale VARCHAR(255) NOT NULL, bought VARCHAR(255) NOT NULL, suppliers INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "sales" (id INT NOT NULL, card INT NOT NULL, flag INT NOT NULL, discount INT NOT NULL, total_with_discount VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, combined_total_text VARCHAR(255) NOT NULL, daysale DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "salesValue" (id INT NOT NULL, resale INT NOT NULL, bought INT NOT NULL, id_clothes INT NOT NULL, id_sales INT NOT NULL, daysale DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "suppliers" (id INT NOT NULL, name VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, permi VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, cpf VARCHAR(255) DEFAULT NULL, rg VARCHAR(255) DEFAULT NULL, dat_nasc VARCHAR(255) DEFAULT NULL, cidade VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE "clothes_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "sales_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "salesValue_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "suppliers_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP TABLE "clothes"');
        $this->addSql('DROP TABLE "sales"');
        $this->addSql('DROP TABLE "salesValue"');
        $this->addSql('DROP TABLE "suppliers"');
        $this->addSql('DROP TABLE "user"');
    }
}
