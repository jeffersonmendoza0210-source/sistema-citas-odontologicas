-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.4.3 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para citas_medicas_db
CREATE DATABASE IF NOT EXISTS `citas_medicas_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `citas_medicas_db`;

-- Volcando estructura para tabla citas_medicas_db.archivos_paciente
CREATE TABLE IF NOT EXISTS `archivos_paciente` (
  `id_archivo` int NOT NULL AUTO_INCREMENT,
  `id_paciente` int NOT NULL,
  `nombre_archivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ruta_archivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tipo_archivo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha_subida` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_archivo`),
  KEY `id_paciente` (`id_paciente`),
  CONSTRAINT `archivos_paciente_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.archivos_paciente: ~6 rows (aproximadamente)
INSERT INTO `archivos_paciente` (`id_archivo`, `id_paciente`, `nombre_archivo`, `ruta_archivo`, `tipo_archivo`, `fecha_subida`) VALUES
	(1, 4, 'Curso de PostgreSQL.pdf', 'DOC_692c322d2092a3.05734163.pdf', 'pdf', '2025-11-30 07:01:49'),
	(2, 4, 'Pagos Virtuales.pdf', 'DOC_69641f21a8ac95.72257473.pdf', 'pdf', '2026-01-11 17:07:29'),
	(3, 4, 'WhatsApp Image 2026-01-02 at 10.14.30.jpeg', 'DOC_6967d8080db507.40721577.jpeg', NULL, '2026-01-14 12:53:12'),
	(4, 9, 'papelete.png', 'DOC_6967d822b85561.39131679.png', NULL, '2026-01-14 12:53:38'),
	(5, 9, 'c', 'DOC_6967de09346960.20882504.pdf', 'pdf', '2026-01-14 13:18:49'),
	(6, 9, 'papelete.png', 'DOC_6967e0c89b4183.99516343.png', '', '2026-01-14 13:30:32'),
	(7, 12, 'ascsa', 'DOC_6967e45ee3bb46.06583259.pdf', NULL, '2026-01-14 13:45:50');

-- Volcando estructura para tabla citas_medicas_db.auditoria
CREATE TABLE IF NOT EXISTS `auditoria` (
  `id_log` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `accion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tabla_afectada` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_registro_afectado` int DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `ip_usuario` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha_hora` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `auditoria_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.auditoria: ~7 rows (aproximadamente)
INSERT INTO `auditoria` (`id_log`, `id_usuario`, `accion`, `tabla_afectada`, `id_registro_afectado`, `descripcion`, `ip_usuario`, `fecha_hora`) VALUES
	(1, 3, 'ATENCION', 'citas', 2, 'Consulta Finalizada', '127.0.0.1', '2025-12-26 14:08:44'),
	(2, 3, 'CREAR', 'citas', 0, 'Cita Agendada', '127.0.0.1', '2026-01-12 16:11:14'),
	(3, 3, 'PAGO', 'pagos', 0, 'Cobro: 50.00', '127.0.0.1', '2026-01-12 16:11:33'),
	(4, 3, 'ATENCION', 'citas', 3, 'Consulta Finalizada', '127.0.0.1', '2026-01-12 16:12:02'),
	(5, 3, 'ELIMINAR', 'citas', 1, 'Cita Borrada', '127.0.0.1', '2026-01-12 16:14:02'),
	(6, 3, 'CREAR', 'citas', 0, 'Cita Agendada', '127.0.0.1', '2026-01-13 12:06:45'),
	(7, 3, 'PAGO', 'pagos', 0, 'Cobro: 70.00', '127.0.0.1', '2026-01-13 12:06:55'),
	(8, 3, 'ATENCION', 'citas', 4, 'Consulta Finalizada', '127.0.0.1', '2026-01-13 12:07:23');

-- Volcando estructura para tabla citas_medicas_db.citas
CREATE TABLE IF NOT EXISTS `citas` (
  `id_cita` int NOT NULL AUTO_INCREMENT,
  `id_paciente` int NOT NULL,
  `id_medico` int NOT NULL,
  `id_servicio` int DEFAULT NULL,
  `fecha_cita` datetime NOT NULL,
  `motivo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `peso` decimal(5,2) DEFAULT NULL,
  `talla` decimal(5,2) DEFAULT NULL,
  `temperatura` decimal(4,1) DEFAULT NULL,
  `presion_arterial` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `diagnostico` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `prescripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `dias_reposo` int DEFAULT '0',
  `fecha_fin_reposo` date DEFAULT NULL,
  `estado` enum('Pendiente','Confirmada','Cancelada','Finalizada') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Pendiente',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_cita`),
  KEY `id_paciente` (`id_paciente`),
  KEY `id_medico` (`id_medico`),
  KEY `fk_cita_servicio` (`id_servicio`),
  CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `citas_ibfk_2` FOREIGN KEY (`id_medico`) REFERENCES `medicos` (`id_medico`),
  CONSTRAINT `fk_cita_servicio` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.citas: ~3 rows (aproximadamente)
INSERT INTO `citas` (`id_cita`, `id_paciente`, `id_medico`, `id_servicio`, `fecha_cita`, `motivo`, `peso`, `talla`, `temperatura`, `presion_arterial`, `diagnostico`, `prescripcion`, `dias_reposo`, `fecha_fin_reposo`, `estado`, `created_at`) VALUES
	(2, 4, 2, 4, '2025-12-02 10:00:00', 'cita de consulta', 120.00, 150.00, 13.0, '12', 'tiene caca', 'que cague', 34, '2026-01-29', 'Finalizada', '2025-11-30 12:41:05'),
	(3, 9, 3, 1, '2026-01-19 16:11:00', 'nose', 30.00, 149.00, 17.0, '13', 'esta bien ', 'ninguna', 0, NULL, 'Finalizada', '2026-01-12 21:11:14'),
	(4, 4, 3, 4, '2026-01-19 14:06:00', 'SCA', 56.00, 123.00, 19.0, '15', 'ESTA BIEN EL GAFO', 'MAMRHUEVO', 12, '2026-01-25', 'Finalizada', '2026-01-13 17:06:45');

-- Volcando estructura para tabla citas_medicas_db.configuracion
CREATE TABLE IF NOT EXISTS `configuracion` (
  `id` int NOT NULL,
  `nombre_clinica` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `direccion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefono` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `moneda` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'S/.',
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.configuracion: ~1 rows (aproximadamente)
INSERT INTO `configuracion` (`id`, `nombre_clinica`, `direccion`, `telefono`, `email`, `moneda`, `logo`) VALUES
	(1, 'ARIDENT', 'Av. Principal 123, Lima', '(01) 555-0000', 'contacto@saludtotal.com', 'S/.', 'logo_clinica_1766774171.png');

-- Volcando estructura para tabla citas_medicas_db.especialidades
CREATE TABLE IF NOT EXISTS `especialidades` (
  `id_especialidad` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_especialidad`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.especialidades: ~5 rows (aproximadamente)
INSERT INTO `especialidades` (`id_especialidad`, `nombre`) VALUES
	(1, 'Medicina General'),
	(3, 'Pediatría'),
	(4, 'Dermatología'),
	(5, 'Ginecología'),
	(6, 'dental');

-- Volcando estructura para tabla citas_medicas_db.horarios_medicos
CREATE TABLE IF NOT EXISTS `horarios_medicos` (
  `id_horario` int NOT NULL AUTO_INCREMENT,
  `id_medico` int NOT NULL,
  `dia_semana` enum('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  PRIMARY KEY (`id_horario`),
  KEY `id_medico` (`id_medico`),
  CONSTRAINT `horarios_medicos_ibfk_1` FOREIGN KEY (`id_medico`) REFERENCES `medicos` (`id_medico`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.horarios_medicos: ~1 rows (aproximadamente)
INSERT INTO `horarios_medicos` (`id_horario`, `id_medico`, `dia_semana`, `hora_inicio`, `hora_fin`) VALUES
	(2, 3, 'Lunes', '00:10:00', '22:10:00'),
	(3, 1, 'Miércoles', '12:23:00', '12:33:00');

-- Volcando estructura para tabla citas_medicas_db.medicamentos
CREATE TABLE IF NOT EXISTS `medicamentos` (
  `id_medicamento` int NOT NULL AUTO_INCREMENT,
  `nombre_comercial` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nombre_generico` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `presentacion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `stock` int DEFAULT '0',
  `estado` enum('Activo','Inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Activo',
  PRIMARY KEY (`id_medicamento`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.medicamentos: ~3 rows (aproximadamente)
INSERT INTO `medicamentos` (`id_medicamento`, `nombre_comercial`, `nombre_generico`, `presentacion`, `stock`, `estado`) VALUES
	(1, 'Aspirina Forte', 'Ácido Acetilsalicílico', 'Tableta 500mg', 100, 'Activo'),
	(2, 'Amoxil', 'Amoxicilina', 'Jarabe 250ml', 50, 'Activo'),
	(3, 'Ibuprofeno', 'Ibuprofeno', 'Cápsula 400mg', 200, 'Activo');

-- Volcando estructura para tabla citas_medicas_db.medicos
CREATE TABLE IF NOT EXISTS `medicos` (
  `id_medico` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_especialidad` int NOT NULL,
  `colegiatura` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_medico`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_especialidad` (`id_especialidad`),
  CONSTRAINT `medicos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `medicos_ibfk_2` FOREIGN KEY (`id_especialidad`) REFERENCES `especialidades` (`id_especialidad`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.medicos: ~2 rows (aproximadamente)
INSERT INTO `medicos` (`id_medico`, `id_usuario`, `id_especialidad`, `colegiatura`) VALUES
	(1, 5, 6, NULL),
	(2, 6, 3, NULL),
	(3, 10, 1, NULL);

-- Volcando estructura para tabla citas_medicas_db.odontogramas
CREATE TABLE IF NOT EXISTS `odontogramas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_paciente` int NOT NULL,
  `pieza` int NOT NULL,
  `cara` varchar(5) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `notas` text,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla citas_medicas_db.odontogramas: ~12 rows (aproximadamente)
INSERT INTO `odontogramas` (`id`, `id_paciente`, `pieza`, `cara`, `estado`, `notas`, `fecha_registro`) VALUES
	(1, 4, 15, 'O', 'obturado', 'cdsc', '2026-01-14 18:04:03'),
	(2, 4, 11, 'V', 'ausente', '', '2026-01-14 18:04:03'),
	(3, 9, 13, 'O', 'obturado', '', '2026-01-14 18:04:26'),
	(4, 9, 11, 'O', 'ausente', '', '2026-01-14 18:04:26'),
	(5, 12, 14, 'O', 'obturado', 'AA', '2026-01-14 18:45:42'),
	(6, 4, 12, 'D', 'caries', '', '2026-01-14 18:49:45'),
	(7, 4, 13, 'O', 'caries', 'jjnjjnj', '2026-01-14 18:51:15'),
	(8, 9, 12, 'O', 'obturado', '', '2026-01-14 18:53:11'),
	(9, 4, 47, 'O', 'caries', '', '2026-01-14 18:56:23'),
	(10, 9, 12, 'M', 'normal', 'asc ds', '2026-01-14 19:16:34'),
	(11, 9, 42, 'D', 'obturado', 'xscs', '2026-01-14 19:16:52'),
	(12, 4, 24, 'O', 'obturado', 'jncnskjcdskjvk', '2026-01-15 01:54:37'),
	(13, 4, 48, 'M', 'caries', 'impactada', '2026-01-15 01:54:37'),
	(14, 13, 15, 'D', 'obturado', ' x', '2026-01-16 20:43:43');

-- Volcando estructura para tabla citas_medicas_db.odontograma_pacientes
CREATE TABLE IF NOT EXISTS `odontograma_pacientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_paciente` int NOT NULL,
  `pieza` int NOT NULL,
  `cara` varchar(2) COLLATE utf8mb4_general_ci NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `notas` text COLLATE utf8mb4_general_ci,
  `fecha_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_paciente` (`id_paciente`),
  CONSTRAINT `odontograma_pacientes_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.odontograma_pacientes: ~0 rows (aproximadamente)

-- Volcando estructura para tabla citas_medicas_db.pagos
CREATE TABLE IF NOT EXISTS `pagos` (
  `id_pago` int NOT NULL AUTO_INCREMENT,
  `id_cita` int NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `fecha_pago` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pago`),
  KEY `id_cita` (`id_cita`),
  CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_cita`) REFERENCES `citas` (`id_cita`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.pagos: ~2 rows (aproximadamente)
INSERT INTO `pagos` (`id_pago`, `id_cita`, `monto`, `metodo_pago`, `observaciones`, `fecha_pago`) VALUES
	(2, 2, 70.00, 'Yape/Plin', 'pago de prueba 2', '2025-11-30 08:16:46'),
	(3, 3, 50.00, 'Tarjeta', '', '2026-01-12 16:11:33'),
	(4, 4, 70.00, 'Tarjeta', '', '2026-01-13 12:06:55');

-- Volcando estructura para tabla citas_medicas_db.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.roles: ~4 rows (aproximadamente)
INSERT INTO `roles` (`id_rol`, `nombre`) VALUES
	(1, 'Administrador'),
	(2, 'Medico'),
	(3, 'Paciente'),
	(4, 'Recepcionista');

-- Volcando estructura para tabla citas_medicas_db.servicios
CREATE TABLE IF NOT EXISTS `servicios` (
  `id_servicio` int NOT NULL AUTO_INCREMENT,
  `nombre_servicio` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `precio` decimal(10,2) NOT NULL,
  `estado` enum('Activo','Inactivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Activo',
  PRIMARY KEY (`id_servicio`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.servicios: ~4 rows (aproximadamente)
INSERT INTO `servicios` (`id_servicio`, `nombre_servicio`, `descripcion`, `precio`, `estado`) VALUES
	(1, 'Consulta Medicina General', NULL, 50.00, 'Activo'),
	(2, 'Consulta Especializada', NULL, 80.00, 'Activo'),
	(3, 'Ecografía Abdominal', NULL, 120.00, 'Activo'),
	(4, 'Limpieza Dental', NULL, 70.00, 'Activo');

-- Volcando estructura para tabla citas_medicas_db.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `documento_identidad` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `grupo_sanguineo` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alergias` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `enfermedades_cronicas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_rol` int DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`),
  KEY `id_rol` (`id_rol`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla citas_medicas_db.usuarios: ~10 rows (aproximadamente)
INSERT INTO `usuarios` (`id_usuario`, `nombre`, `documento_identidad`, `email`, `telefono`, `grupo_sanguineo`, `alergias`, `enfermedades_cronicas`, `password`, `avatar`, `id_rol`, `fecha_creacion`) VALUES
	(3, 'ADMINISTRADOR', NULL, 'admin@medico.com', NULL, NULL, NULL, NULL, '$2y$10$MF5RgSRTulSRsgYkwT6PxufDyw4romec4x2wFcm63/ON5nSoYXiv2', 'avatar_3_1766774566.png', 1, '2025-11-29 14:40:16'),
	(4, 'CARLOS RAMIREZ', '41236598', 'carlosramirez@correo.com', '966648329', '', '', '', '$2y$10$zArmYUpQiv6G/swX72gQ7uteeJe7cC0QpIt0dr5wh7VvQfZ/HsIMa', NULL, 3, '2025-11-29 15:21:20'),
	(5, 'Jefferson Rodrigo Mendoza Monsalve', NULL, 'medico1@correo.com', NULL, NULL, NULL, NULL, '$2y$10$Jc.FwMdeWR0dhFY34RfGQusMsdF0CEDDu1npH3XlRQ9VkAtFJqlPi', NULL, 2, '2025-11-29 15:21:57'),
	(6, 'rodrigo', NULL, 'medico2@medico.com', NULL, NULL, NULL, NULL, '$2y$10$L8KGFapU7oYOgqLxIAQrcebgzhYqPdug1HgzHo.LqRxB0RAxTQKs6', NULL, 2, '2025-11-30 11:51:14'),
	(7, 'Ana Recepción', NULL, 'recepcion@medico.com', NULL, NULL, NULL, NULL, '$2y$10$lo5u63zTLVOHwzK7PYFe5uxa1U31LLmNsnjjxjNPszGP/aSPB8QdW', NULL, 4, '2025-12-01 19:46:23'),
	(8, 'Nuevo Usuario', NULL, 'admin1@medico.com', '923624655', NULL, NULL, NULL, '$2y$10$4M.RkRkYm4zY1xrxyJhKYekNH8.qKNnXS7rZswRUKomf.GrL.Mrua', NULL, NULL, '2025-12-26 18:02:43'),
	(9, 'jeferson mendoza', '41236598', 'jeferson@gmail.com', '923624655', 'O+', '', '', '$2y$10$bo8jSui5buVKqV27nCvhpOkh0X1R5PQO8sXFuQ349vsERHztklkEi', NULL, 3, '2026-01-12 20:17:25'),
	(10, 'sansumg', NULL, 'notiene@gmail.com', NULL, NULL, NULL, NULL, '$2y$10$1mQEr2mTaJhQfqUV9PdFMuGQKzuDkjh06WFerEWyBZAgQ1vJh0Gsi', NULL, 2, '2026-01-12 21:06:59'),
	(12, 'Jefferson Rodrigo Mendoza Monsalve', '4123444', 'admin1232@medico.com', '923624655', 'O+', '', '', '$2y$10$oeUcsqOdMKxQwxXFMXIBr.AHAKXliZofLqbKqLAYM98.59x5IwbQq', NULL, 3, '2026-01-14 16:54:13'),
	(13, 'Jefferson Rodrigo Mendoza Monsalve', 'jcjdsjcsdj', 'inventario@correo.com', '923624655', '', '', '', '$2y$10$Yvm5Di740.tAOhoDhY2jMOhOLiNWwc2dRKZWwJMwnpbZyZOUX0hMq', NULL, 3, '2026-01-16 20:39:53');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
