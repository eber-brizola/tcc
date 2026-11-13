-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 13/11/2026 às 14:49
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `docs_ceedo`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nome_categoria` varchar(255) NOT NULL,
  `info_categoria` varchar(255) NOT NULL DEFAULT '',
  `icone` varchar(50) NOT NULL DEFAULT 'bi-folder'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nome_categoria`, `info_categoria`, `icone`) VALUES
(24, 'Atas', 'Atas realizadas no ambiente escolar', 'bi-file-earmark-text'),
(25, 'Regimentos', 'Regimentos escolares', 'bi-folder-check'),
(26, 'Leis', 'Leis educacionais', 'bi-bank'),
(27, 'TCC (Trabalhos de Conclusão de Curso)', 'Trabalhos realizados com fins de mostrar os conhecimentos adquiridos no curso', 'bi-journal-text');

-- --------------------------------------------------------

--
-- Estrutura para tabela `documentos`
--

CREATE TABLE `documentos` (
  `id_documento` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `caminho` varchar(255) NOT NULL,
  `data_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `documentos`
--

INSERT INTO `documentos` (`id_documento`, `nome`, `id_categoria`, `caminho`, `data_upload`) VALUES
(17, 'teste ata', 24, 'uploads/d1761ff2999a4dabb9e94653e8332810.pdf', '2026-11-06 12:52:22'),
(18, 'teste leis', 26, 'uploads/1dfd42966d81fde89c5264157f83fe04.pdf', '2026-11-06 12:52:33'),
(19, 'teste regimentos', 25, 'uploads/023de384e99c322691944f6172735c7c.jpg', '2026-11-06 12:52:44');

-- --------------------------------------------------------

--
-- Estrutura para tabela `login_tentativas`
--

CREATE TABLE `login_tentativas` (
  `chave` char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `tentativas` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `inicio_janela` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `login_tentativas`
--

INSERT INTO `login_tentativas` (`chave`, `tentativas`, `inicio_janela`) VALUES
('bc6729088c4a2271f55a0eb55136ffe6bb18d81c8859afbe3a9a65edd91ce352', 6, '2026-10-26 09:38:52'),
('dc73fa592e912b14afb35f2937e86ccfadca4917dd62cf929afcef7c49c6b572', 1, '2026-10-26 16:03:44');

-- --------------------------------------------------------

--
-- Estrutura para tabela `operadores`
--

CREATE TABLE `operadores` (
  `id_operador` int(11) NOT NULL,
  `nome_operador` varchar(255) NOT NULL,
  `email_operador` varchar(255) NOT NULL,
  `senha_operador` varchar(255) NOT NULL,
  `nivel` enum('adm','root') NOT NULL DEFAULT 'adm',
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `versao_sessao` int(10) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `operadores`
--

INSERT INTO `operadores` (`id_operador`, `nome_operador`, `email_operador`, `senha_operador`, `nivel`, `ativo`, `versao_sessao`) VALUES
(2, 'CEEDO Root', 'ceedo@ceedo.com.br', '$2y$10$y.4rL5m3etBxyje1FU7Qe.yB0MZG.eYFoN8MGbdlNIp9A9xWLuMPu', 'root', 1, 1),
(3, 'eber', 'eber@eber.com.br', '$2y$10$vPhNr/SGpfeCrzLAGRfInOQ96hRLJlAN5bwYauQcKEsQrfE5Hg4Ca', 'root', 1, 1),
(5, 'testes', 'teste@teste.com', '$2y$10$ICq6sjd6wNFMB9x4SzPMouZTO4Q3rUxVxZTgf7zOu5zfKdJs5Zb7a', 'adm', 1, 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `root_confirmacoes`
--

CREATE TABLE `root_confirmacoes` (
  `id_operador` int(11) NOT NULL,
  `tentativas` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `inicio_janela` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `root_confirmacoes`
--

INSERT INTO `root_confirmacoes` (`id_operador`, `tentativas`, `inicio_janela`) VALUES
(2, 1, '2026-10-28 11:07:04'),
(3, 1, '2026-11-13 10:04:02');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Índices de tabela `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id_documento`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Índices de tabela `login_tentativas`
--
ALTER TABLE `login_tentativas`
  ADD PRIMARY KEY (`chave`);

--
-- Índices de tabela `operadores`
--
ALTER TABLE `operadores`
  ADD PRIMARY KEY (`id_operador`),
  ADD UNIQUE KEY `uq_operadores_email` (`email_operador`);

--
-- Índices de tabela `root_confirmacoes`
--
ALTER TABLE `root_confirmacoes`
  ADD PRIMARY KEY (`id_operador`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de tabela `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id_documento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `operadores`
--
ALTER TABLE `operadores`
  MODIFY `id_operador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `documentos`
--
ALTER TABLE `documentos`
  ADD CONSTRAINT `documentos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
