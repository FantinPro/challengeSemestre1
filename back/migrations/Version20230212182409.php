<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230212182409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE message_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE post_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pub_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE report_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE share_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE stat_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE token_reset_password_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_to_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE category (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE message (id INT NOT NULL, creator_id INT NOT NULL, parent_id INT DEFAULT NULL, content VARCHAR(255) NOT NULL, is_deleted BOOLEAN NOT NULL, edited_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B6BD307F61220EA6 ON message (creator_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F727ACA70 ON message (parent_id)');
        $this->addSql('CREATE TABLE message_user (message_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(message_id, user_id))');
        $this->addSql('CREATE INDEX IDX_24064D90537A1329 ON message_user (message_id)');
        $this->addSql('CREATE INDEX IDX_24064D90A76ED395 ON message_user (user_id)');
        $this->addSql('CREATE TABLE post (id INT NOT NULL, category_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, toto VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D12469DE2 ON post (category_id)');
        $this->addSql('CREATE TABLE pub (id INT NOT NULL, owner_id INT NOT NULL, start_date TIMESTAMP(0) WITH TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITH TIME ZONE NOT NULL, message VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, status VARCHAR(30) NOT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, payment_intent_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5A443C857E3C61F9 ON pub (owner_id)');
        $this->addSql('CREATE TABLE report (id INT NOT NULL, reporting_user_id INT NOT NULL, reported_message_id INT NOT NULL, type VARCHAR(30) NOT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C42F7784713FF03D ON report (reporting_user_id)');
        $this->addSql('CREATE INDEX IDX_C42F7784387BD835 ON report (reported_message_id)');
        $this->addSql('CREATE TABLE share (id INT NOT NULL, sharing_by_id INT DEFAULT NULL, shared_message_id INT DEFAULT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EF069D5AAF5FCA45 ON share (sharing_by_id)');
        $this->addSql('CREATE INDEX IDX_EF069D5A2769F3E6 ON share (shared_message_id)');
        $this->addSql('CREATE TABLE stat (id INT NOT NULL, ad_id INT DEFAULT NULL, from_user_id INT DEFAULT NULL, click BOOLEAN DEFAULT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_20B8FF214F34D596 ON stat (ad_id)');
        $this->addSql('CREATE INDEX IDX_20B8FF212130303A ON stat (from_user_id)');
        $this->addSql('CREATE TABLE token_reset_password (id INT NOT NULL, from_user_id INT NOT NULL, expire_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, token VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_904F3E032130303A ON token_reset_password (from_user_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified BOOLEAN DEFAULT false NOT NULL, profile_picture VARCHAR(255) DEFAULT NULL, pseudo VARCHAR(25) NOT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, bio VARCHAR(255) DEFAULT NULL, stripe_customer_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64986CC499D ON "user" (pseudo)');
        $this->addSql('CREATE TABLE user_to_user (id INT NOT NULL, me_id INT NOT NULL, other_id INT NOT NULL, status VARCHAR(30) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3270D98C32785FF2 ON user_to_user (me_id)');
        $this->addSql('CREATE INDEX IDX_3270D98C998D9879 ON user_to_user (other_id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F61220EA6 FOREIGN KEY (creator_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F727ACA70 FOREIGN KEY (parent_id) REFERENCES message (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message_user ADD CONSTRAINT FK_24064D90537A1329 FOREIGN KEY (message_id) REFERENCES message (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message_user ADD CONSTRAINT FK_24064D90A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pub ADD CONSTRAINT FK_5A443C857E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784713FF03D FOREIGN KEY (reporting_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784387BD835 FOREIGN KEY (reported_message_id) REFERENCES message (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE share ADD CONSTRAINT FK_EF069D5AAF5FCA45 FOREIGN KEY (sharing_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE share ADD CONSTRAINT FK_EF069D5A2769F3E6 FOREIGN KEY (shared_message_id) REFERENCES message (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stat ADD CONSTRAINT FK_20B8FF214F34D596 FOREIGN KEY (ad_id) REFERENCES pub (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stat ADD CONSTRAINT FK_20B8FF212130303A FOREIGN KEY (from_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE token_reset_password ADD CONSTRAINT FK_904F3E032130303A FOREIGN KEY (from_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_to_user ADD CONSTRAINT FK_3270D98C32785FF2 FOREIGN KEY (me_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_to_user ADD CONSTRAINT FK_3270D98C998D9879 FOREIGN KEY (other_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE message_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE post_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pub_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE report_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE share_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE stat_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE token_reset_password_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE user_to_user_id_seq CASCADE');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F61220EA6');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F727ACA70');
        $this->addSql('ALTER TABLE message_user DROP CONSTRAINT FK_24064D90537A1329');
        $this->addSql('ALTER TABLE message_user DROP CONSTRAINT FK_24064D90A76ED395');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8D12469DE2');
        $this->addSql('ALTER TABLE pub DROP CONSTRAINT FK_5A443C857E3C61F9');
        $this->addSql('ALTER TABLE report DROP CONSTRAINT FK_C42F7784713FF03D');
        $this->addSql('ALTER TABLE report DROP CONSTRAINT FK_C42F7784387BD835');
        $this->addSql('ALTER TABLE share DROP CONSTRAINT FK_EF069D5AAF5FCA45');
        $this->addSql('ALTER TABLE share DROP CONSTRAINT FK_EF069D5A2769F3E6');
        $this->addSql('ALTER TABLE stat DROP CONSTRAINT FK_20B8FF214F34D596');
        $this->addSql('ALTER TABLE stat DROP CONSTRAINT FK_20B8FF212130303A');
        $this->addSql('ALTER TABLE token_reset_password DROP CONSTRAINT FK_904F3E032130303A');
        $this->addSql('ALTER TABLE user_to_user DROP CONSTRAINT FK_3270D98C32785FF2');
        $this->addSql('ALTER TABLE user_to_user DROP CONSTRAINT FK_3270D98C998D9879');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE message_user');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE pub');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE share');
        $this->addSql('DROP TABLE stat');
        $this->addSql('DROP TABLE token_reset_password');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_to_user');
    }
}
