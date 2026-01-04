<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260103212054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE address (id INT UNSIGNED AUTO_INCREMENT NOT NULL, street VARCHAR(64) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, city VARCHAR(45) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, zip VARCHAR(10) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, country VARCHAR(45) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE item_with_lot (id INT UNSIGNED AUTO_INCREMENT NOT NULL, item_id INT UNSIGNED NOT NULL, lot VARCHAR(40) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, added DATETIME NOT NULL, INDEX item_id (item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE client (id INT UNSIGNED AUTO_INCREMENT NOT NULL, address_id INT UNSIGNED NOT NULL, company_name VARCHAR(64) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_general_ci`, forname VARCHAR(45) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, surname VARCHAR(45) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, middlename VARCHAR(45) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_general_ci`, title VARCHAR(45) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_general_ci`, email VARCHAR(45) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, phone VARCHAR(45) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_general_ci`, added DATE NOT NULL, note VARCHAR(128) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_general_ci`, UNIQUE INDEX id_UNIQUE (id), INDEX address_id (address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE manufacturer (id INT UNSIGNED AUTO_INCREMENT NOT NULL, address_id INT UNSIGNED NOT NULL, name VARCHAR(64) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, email VARCHAR(45) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, phone VARCHAR(45) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_general_ci`, UNIQUE INDEX id_UNIQUE (id), INDEX address_id (address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE warehouse_has_item (id INT UNSIGNED AUTO_INCREMENT NOT NULL, warehouse_id INT UNSIGNED NOT NULL, item_with_lot_id INT UNSIGNED NOT NULL, order_id INT UNSIGNED DEFAULT NULL, status_id INT UNSIGNED NOT NULL, added DATE NOT NULL, UNIQUE INDEX id_UNIQUE (id), INDEX war_id (warehouse_id), INDEX ord_id (order_id), INDEX item_with_lot_id (item_with_lot_id), INDEX status_id (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE warehouse (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(64) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, area INT NOT NULL, created DATE NOT NULL, last_edited DATE DEFAULT NULL, UNIQUE INDEX id_UNIQUE (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE orders (id INT UNSIGNED AUTO_INCREMENT NOT NULL, client_id INT UNSIGNED NOT NULL, status_id INT UNSIGNED NOT NULL, added DATETIME NOT NULL, last_edited DATETIME DEFAULT NULL, note VARCHAR(128) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_general_ci`, UNIQUE INDEX id_UNIQUE (id), INDEX client_id (client_id), INDEX status_id (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE order_status (id INT UNSIGNED AUTO_INCREMENT NOT NULL, short_name VARCHAR(32) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, name VARCHAR(64) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE item (id INT UNSIGNED AUTO_INCREMENT NOT NULL, manufacturer_id INT UNSIGNED NOT NULL, name VARCHAR(64) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, area DOUBLE PRECISION NOT NULL, added DATETIME NOT NULL, UNIQUE INDEX id_UNIQUE (id), INDEX manufacturer_id (manufacturer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE order_has_item (id INT UNSIGNED AUTO_INCREMENT NOT NULL, order_id INT UNSIGNED NOT NULL, item_id INT UNSIGNED NOT NULL, amount INT NOT NULL, UNIQUE INDEX id_UNIQUE (id), INDEX order_id (order_id), INDEX item_id (item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('CREATE TABLE item_status (id INT UNSIGNED AUTO_INCREMENT NOT NULL, short_name VARCHAR(32) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, name VARCHAR(32) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_general_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        
        $this->addData();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE address');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE item_with_lot');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE client');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE manufacturer');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE warehouse_has_item');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE warehouse');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE orders');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE order_status');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE item');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE order_has_item');
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQL80Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MySQL80Platform'."
        );

        $this->addSql('DROP TABLE item_status');
    }
    
    private function addData(): void
    {
        $this->addSql("INSERT INTO item_status (short_name, name) VALUES 
            ('available', 'volná'), 
            ('reserved', 'rezervovaná'), 
            ('sent_off', 'odeslaná'), 
            ('returned', 'reklamovaná')")
        ;
        
        $this->addSql("INSERT INTO order_status (short_name, name) VALUES 
            ('new', 'Nová'), 
            ('items_reserved', 'Připravena k odeslání'), 
            ('sent_off', 'Expedovaná'), 
            ('complain_in_progress', 'Probíhá reklamace'), 
            ('items_returned', 'Reklamace vyřízena (zboží vráceno)'), 
            ('storno', 'Storno')")
        ;
    }
}
