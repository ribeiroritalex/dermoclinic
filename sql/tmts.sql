-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 08-Abr-2022 às 20:23
-- Versão do servidor: 10.4.22-MariaDB
-- versão do PHP: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `tmts`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `appointment`
--

CREATE TABLE `appointment` (
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `appointment_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `diagnostic`
--

CREATE TABLE `diagnostic` (
  `diagnostic_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `blood_pressure` float DEFAULT NULL,
  `weight` float DEFAULT NULL,
  `height` float DEFAULT NULL,
  `observations` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `prescription`
--

CREATE TABLE `prescription` (
  `prescription_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `prescription_type` int(11) DEFAULT NULL,
  `quantity` varchar(100) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `questions`
--

CREATE TABLE `questions` (
  `question_id` int(11) NOT NULL,
  `screening_type` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `screening`
--

CREATE TABLE `screening` (
  `screening_id` int(11) NOT NULL,
  `screening_type` int(11) DEFAULT NULL,
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `screening_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `screening_images`
--

CREATE TABLE `screening_images` (
  `image_id` int(11) NOT NULL,
  `screening_id` int(11) DEFAULT NULL,
  `image_blob` blob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `screening_questions`
--

CREATE TABLE `screening_questions` (
  `question_id` int(11) NOT NULL,
  `screening_id` int(11) NOT NULL,
  `screening_type` int(11) DEFAULT NULL,
  `answer` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `user_state` int(11) DEFAULT NULL,
  `role` int(11) DEFAULT NULL,
  `phone` varchar(9) DEFAULT NULL,
  `nif` varchar(9) DEFAULT NULL,
  `license_id` varchar(20) DEFAULT NULL,
  `gender` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `birthdate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `address` varchar(255) DEFAULT NULL,
  `pwd` varchar(255) DEFAULT NULL,
  `login_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`user_id`, `email`, `user_state`, `role`, `phone`, `nif`, `license_id`, `gender`, `name`, `birthdate`, `address`, `pwd`, `login_token`) VALUES
(1, 'emailexemplo@teste.com', 1, 0, '123456789', '123456789', 'C-78956', 0, NULL, '2022-03-27 18:32:27', 'Exemplo Address', 'Pass1.', 'lToken'),
(2, 'emailexemplo@teste.com', 1, 0, '123456789', '123456789', 'C-78956', 0, NULL, '2022-03-27 18:33:47', 'Exemplo Address', 'Pass1.', 'lToken'),
(3, 'emailexemplo@teste.com', 1, 0, '123456789', '123456789', 'C-78956', 0, NULL, '2022-03-27 18:33:47', 'Exemplo Address', 'Pass1.', 'lToken'),
(4, 'emailexemplo@teste.com', 1, 0, '123456789', '123456789', 'C-78956', 0, NULL, '2022-03-27 18:33:47', 'Exemplo Address', 'Pass1.', 'lToken'),
(5, 'emailexemplo@teste.com', 1, 0, '123456789', '123456789', 'C-78956', 0, NULL, '2022-03-27 18:33:47', 'Exemplo Address', 'Pass1.', 'lToken'),
(6, 'emailexemplo@teste.com', 1, 0, '123456789', '123456789', 'C-78956', 0, NULL, '2022-03-27 18:33:47', 'Exemplo Address', 'Pass1.', 'lToken'),
(7, 'emailexemplo@teste.com', 1, 0, '123456789', '123456789', 'C-78956', 0, NULL, '2022-03-27 18:33:47', 'Exemplo Address', 'Pass1.', 'lToken'),
(8, 'emailexemplo@teste.c13231231om', 1, 0, '999999', '4444', 'C-78956', 0, NULL, '2022-03-27 18:40:21', 'Exemplo Address', 'Pass1.', 'lToken'),
(9, 'emailexemplo@te1231ste.com', 0, 0, '123456789', '123456789', 'C-78956', 0, NULL, '2022-03-27 18:34:27', 'Exemplo Address', 'Pass1.', 'lToken'),
(10, 'emailexemp1231lo@teste.com', 0, 0, '123456789', '123456789', 'C-78956', 0, NULL, '2022-03-27 18:34:27', 'Exemplo Address', 'Pass1.', 'lToken'),
(11, 'ema2311ilexemplo@teste.com', 2, 0, '123456789', '123456789', 'C-78956', 0, NULL, '2022-03-27 18:34:27', 'Exemplo Address', 'Pass1.', 'lToken'),
(12, 'emailex123emplo@teste.com', 2, 0, '123456789', '123456789', 'C-78956', 0, NULL, '2022-03-27 18:34:27', 'Exemplo Address', 'Pass1.', 'lToken');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Índices para tabela `diagnostic`
--
ALTER TABLE `diagnostic`
  ADD PRIMARY KEY (`diagnostic_id`,`appointment_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Índices para tabela `prescription`
--
ALTER TABLE `prescription`
  ADD PRIMARY KEY (`prescription_id`,`appointment_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Índices para tabela `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`,`screening_type`);

--
-- Índices para tabela `screening`
--
ALTER TABLE `screening`
  ADD PRIMARY KEY (`screening_id`,`appointment_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Índices para tabela `screening_images`
--
ALTER TABLE `screening_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `screening_id` (`screening_id`);

--
-- Índices para tabela `screening_questions`
--
ALTER TABLE `screening_questions`
  ADD PRIMARY KEY (`question_id`,`screening_id`),
  ADD KEY `question_id` (`question_id`,`screening_type`),
  ADD KEY `screening_id` (`screening_id`);

--
-- Índices para tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `diagnostic`
--
ALTER TABLE `diagnostic`
  MODIFY `diagnostic_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `prescription`
--
ALTER TABLE `prescription`
  MODIFY `prescription_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `screening`
--
ALTER TABLE `screening`
  MODIFY `screening_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `screening_images`
--
ALTER TABLE `screening_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`user_id`);

--
-- Limitadores para a tabela `diagnostic`
--
ALTER TABLE `diagnostic`
  ADD CONSTRAINT `diagnostic_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointment` (`appointment_id`);

--
-- Limitadores para a tabela `prescription`
--
ALTER TABLE `prescription`
  ADD CONSTRAINT `prescription_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointment` (`appointment_id`);

--
-- Limitadores para a tabela `screening`
--
ALTER TABLE `screening`
  ADD CONSTRAINT `screening_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointment` (`appointment_id`);

--
-- Limitadores para a tabela `screening_images`
--
ALTER TABLE `screening_images`
  ADD CONSTRAINT `screening_images_ibfk_1` FOREIGN KEY (`screening_id`) REFERENCES `screening` (`screening_id`);

--
-- Limitadores para a tabela `screening_questions`
--
ALTER TABLE `screening_questions`
  ADD CONSTRAINT `screening_questions_ibfk_1` FOREIGN KEY (`question_id`,`screening_type`) REFERENCES `questions` (`question_id`, `screening_type`),
  ADD CONSTRAINT `screening_questions_ibfk_2` FOREIGN KEY (`screening_id`) REFERENCES `screening` (`screening_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
