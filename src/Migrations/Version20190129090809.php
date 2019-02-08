<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190129090809 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sgl_centers (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, phone VARCHAR(50) NOT NULL, email VARCHAR(254) NOT NULL, address LONGTEXT NOT NULL, zip_code VARCHAR(18) NOT NULL, city VARCHAR(189) NOT NULL, country VARCHAR(90) NOT NULL, center_code INT NOT NULL, center_image LONGTEXT DEFAULT NULL, published TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sgl_customers (id INT AUTO_INCREMENT NOT NULL, preferred_center_id INT DEFAULT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(140) NOT NULL, nickname VARCHAR(100) DEFAULT NULL, email VARCHAR(254) DEFAULT NULL, password VARCHAR(64) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, address LONGTEXT NOT NULL, zip_code VARCHAR(18) NOT NULL, city VARCHAR(189) NOT NULL, country VARCHAR(90) NOT NULL, customer_code INT NOT NULL, registration_date DATETIME NOT NULL, birth_date DATETIME DEFAULT NULL, is_active TINYINT(1) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', image_profile LONGTEXT DEFAULT NULL, INDEX IDX_C20B50FA84294982 (preferred_center_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sgl_customer_activity (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, games_played INT DEFAULT NULL, games_won INT DEFAULT NULL, solo_victories INT DEFAULT NULL, team_victories INT DEFAULT NULL, tournaments_played INT DEFAULT NULL, tournaments_won INT DEFAULT NULL, max_consecutive_games_won INT DEFAULT NULL, average_misses_per_game INT DEFAULT NULL, average_hits_per_game INT DEFAULT NULL, average_points_per_game INT DEFAULT NULL, total_points_all_time INT DEFAULT NULL, friends_invited_to_games INT DEFAULT NULL, customers_sponsored INT DEFAULT NULL, average_spending_per_month DOUBLE PRECISION DEFAULT NULL, total_spending_all_time DOUBLE PRECISION DEFAULT NULL, average_activities_per_month INT DEFAULT NULL, total_activities_all_time INT DEFAULT NULL, last_activity DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_7CDDD2A09395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sgl_loyalty_cards (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, center_id INT DEFAULT NULL, card_code INT NOT NULL, qrcode VARCHAR(340) DEFAULT NULL, date_of_issue DATETIME NOT NULL, is_valid TINYINT(1) NOT NULL, is_phone_app_active TINYINT(1) NOT NULL, loyalty_points INT NOT NULL, status VARCHAR(255) DEFAULT NULL, INDEX IDX_ED6D7CD09395C3F3 (customer_id), INDEX IDX_ED6D7CD05932F377 (center_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sgl_loyalty_cards_requests (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, date_of_request DATETIME NOT NULL, status INT NOT NULL, INDEX IDX_FA0673AE9395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sgl_users (id INT AUTO_INCREMENT NOT NULL, center_id INT DEFAULT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(140) NOT NULL, email VARCHAR(254) NOT NULL, password VARCHAR(64) NOT NULL, is_active TINYINT(1) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_96668C5B5932F377 (center_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sgl_customers ADD CONSTRAINT FK_C20B50FA84294982 FOREIGN KEY (preferred_center_id) REFERENCES sgl_centers (id)');
        $this->addSql('ALTER TABLE sgl_customer_activity ADD CONSTRAINT FK_7CDDD2A09395C3F3 FOREIGN KEY (customer_id) REFERENCES sgl_customers (id)');
        $this->addSql('ALTER TABLE sgl_loyalty_cards ADD CONSTRAINT FK_ED6D7CD09395C3F3 FOREIGN KEY (customer_id) REFERENCES sgl_customers (id)');
        $this->addSql('ALTER TABLE sgl_loyalty_cards ADD CONSTRAINT FK_ED6D7CD05932F377 FOREIGN KEY (center_id) REFERENCES sgl_centers (id)');
        $this->addSql('ALTER TABLE sgl_loyalty_cards_requests ADD CONSTRAINT FK_FA0673AE9395C3F3 FOREIGN KEY (customer_id) REFERENCES sgl_customers (id)');
        $this->addSql('ALTER TABLE sgl_users ADD CONSTRAINT FK_96668C5B5932F377 FOREIGN KEY (center_id) REFERENCES sgl_centers (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sgl_customers DROP FOREIGN KEY FK_C20B50FA84294982');
        $this->addSql('ALTER TABLE sgl_loyalty_cards DROP FOREIGN KEY FK_ED6D7CD05932F377');
        $this->addSql('ALTER TABLE sgl_users DROP FOREIGN KEY FK_96668C5B5932F377');
        $this->addSql('ALTER TABLE sgl_customer_activity DROP FOREIGN KEY FK_7CDDD2A09395C3F3');
        $this->addSql('ALTER TABLE sgl_loyalty_cards DROP FOREIGN KEY FK_ED6D7CD09395C3F3');
        $this->addSql('ALTER TABLE sgl_loyalty_cards_requests DROP FOREIGN KEY FK_FA0673AE9395C3F3');
        $this->addSql('DROP TABLE sgl_centers');
        $this->addSql('DROP TABLE sgl_customers');
        $this->addSql('DROP TABLE sgl_customer_activity');
        $this->addSql('DROP TABLE sgl_loyalty_cards');
        $this->addSql('DROP TABLE sgl_loyalty_cards_requests');
        $this->addSql('DROP TABLE sgl_users');
    }
}
