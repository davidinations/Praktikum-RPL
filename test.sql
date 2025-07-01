-- MySQL database export

START TRANSACTION;

CREATE TABLE IF NOT EXISTS `input_user` (
    `id_data` INT NOT NULL,
    `id_input` INT,
    `id_user` INT,
    `id_kriteria` INT,
    `value` INT,
    `created_at` DATETIME,
    PRIMARY KEY (`id_data`)
);


CREATE TABLE IF NOT EXISTS `master_kriteria` (
    `id_kriteria` INT NOT NULL,
    `id_admin` INT,
    `nama` VARCHAR(255),
    `satuan` VARCHAR(255),
    `bobot` FLOAT,
    `created_at` DATETIME,
    PRIMARY KEY (`id_kriteria`)
);


CREATE TABLE IF NOT EXISTS `hasil_input_user` (
    `id_hasil_input` INT NOT NULL,
    `id_input` INT,
    `id_laptop` INT,
    `rating` FLOAT,
    `ranking` INT,
    `created_at` DATETIME,
    PRIMARY KEY (`id_hasil_input`)
);


CREATE TABLE IF NOT EXISTS `master_laptop` (
    `id_laptop` INT NOT NULL,
    `id_admin` INT,
    `merek` VARCHAR(255),
    `model` VARCHAR(255),
    `harga` INT,
    `processor` VARCHAR(255),
    `ram` INT,
    `storage` INT,
    `gpu` VARCHAR(255),
    `ukuran_baterai` INT,
    `gambar` TEXT,
    `created_at` DATETIME,
    PRIMARY KEY (`id_laptop`)
);


CREATE TABLE IF NOT EXISTS `User` (
    `id_user` INT NOT NULL,
    `username` VARCHAR(255),
    `password` VARCHAR(255),
    `email` VARCHAR(255),
    `created_at` DATETIME,
    PRIMARY KEY (`id_user`)
);


CREATE TABLE IF NOT EXISTS `Admin` (
    `id_admin` INT NOT NULL,
    `username_admin` VARCHAR(255),
    `password` VARCHAR(255),
    `created_at` DATETIME,
    PRIMARY KEY (`id_admin`)
);


CREATE TABLE IF NOT EXISTS `detail_hasil_input_user` (
    `id_detail` INT NOT NULL,
    `id_hasil_input` INT,
    `id_kriteria` INT,
    `hasil_kalkulasi` FLOAT,
    `created_at` DATETIME,
    PRIMARY KEY (`id_detail`)
);


-- Foreign key constraints

ALTER TABLE `Admin`
ADD CONSTRAINT `fk_Admin_id_admin` FOREIGN KEY(`id_admin`) REFERENCES `master_kriteria`(`id_admin`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `hasil_input_user`
ADD CONSTRAINT `fk_hasil_input_user_id_hasil_input` FOREIGN KEY(`id_hasil_input`) REFERENCES `detail_hasil_input_user`(`id_hasil_input`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `hasil_input_user`
ADD CONSTRAINT `fk_hasil_input_user_id_input` FOREIGN KEY(`id_input`) REFERENCES `input_user`(`id_input`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `master_kriteria`
ADD CONSTRAINT `fk_master_kriteria_id_kriteria` FOREIGN KEY(`id_kriteria`) REFERENCES `input_user`(`id_kriteria`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `master_kriteria`
ADD CONSTRAINT `fk_master_kriteria_id_kriteria` FOREIGN KEY(`id_kriteria`) REFERENCES `detail_hasil_input_user`(`id_kriteria`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `master_laptop`
ADD CONSTRAINT `fk_master_laptop_id_admin` FOREIGN KEY(`id_admin`) REFERENCES `Admin`(`id_admin`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `master_laptop`
ADD CONSTRAINT `fk_master_laptop_id_laptop` FOREIGN KEY(`id_laptop`) REFERENCES `hasil_input_user`(`id_laptop`)
ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE `User`
ADD CONSTRAINT `fk_User_id_user` FOREIGN KEY(`id_user`) REFERENCES `input_user`(`id_user`)
ON UPDATE CASCADE ON DELETE RESTRICT;

COMMIT;
