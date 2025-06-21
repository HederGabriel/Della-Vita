CREATE DATABASE  IF NOT EXISTS `pizzaria` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `pizzaria`;
-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: pizzaria
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varbinary(128) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `avatar` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (18,'Heder','hedergabrielsalessousa@gmail.com',_binary '$2y$10$/27BWN1U/qem7ZVAXjFfr.WbF1dFzoNm71iZhHsRnMYW9x8pphYv6',NULL,'../IMG/Profile/01.png'),(19,'Ricka','alves.rickaelly@gmail.com',_binary '$2y$10$sEBku8Fe1./rTL0K5AS5U.tix4RaW86t5e47pvzmf/wqBNmRqHOMy',NULL,'../IMG/Profile/04.png'),(20,'Heder','heder@gmail.com',_binary '$2y$10$bBUkj0UkvY.i3USudsREg.T0VRmnP2xmoaxuINkupvSfPsACuCgj6',NULL,NULL),(21,'Heder','heder1@gmail.com',_binary '$2y$10$2OE5QS3RlGVxNpx/dMRPOOPqa4zlrj6PHStDC1eHzDVVR6I8Vc/M6',NULL,NULL),(22,'ADM|Cozinha','dellavitaenterprise@gmail.com',_binary '$2y$10$DX3bHkJw8Wv43vkWfhYOdOL2v66IZ7F7uuFFxUHsPa2gqsnvxFX.K',NULL,NULL),(23,'mariana','marianavbarross@gmail.com',_binary '$2y$10$cGW2E46XWiAx1HxvAeSS4..fq4YcT7Y16/974qqogqbeGx.AhisVS',NULL,NULL),(24,'Paulo','teste@gmail.com',_binary '$2y$10$DzDkhoqjEj9tbtmHhMecnuxhp.I0ZZO40zXyW7BZexRexb6z2.vki',NULL,NULL);
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enderecos`
--

DROP TABLE IF EXISTS `enderecos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `enderecos` (
  `id_endereco` int(11) NOT NULL AUTO_INCREMENT,
  `rua` varbinary(150) NOT NULL,
  `numero` varbinary(10) DEFAULT NULL,
  `setor` varbinary(100) DEFAULT NULL,
  `cep` varbinary(10) NOT NULL,
  `complemento` varbinary(250) DEFAULT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  `cidade` varbinary(100) NOT NULL,
  PRIMARY KEY (`id_endereco`),
  KEY `id_pedido` (`id_pedido`),
  CONSTRAINT `enderecos_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enderecos`
--

LOCK TABLES `enderecos` WRITE;
/*!40000 ALTER TABLE `enderecos` DISABLE KEYS */;
INSERT INTO `enderecos` VALUES (11,_binary 'Rua Fulano de Tal',_binary '123456',_binary 'Centro',_binary '73906144','',46,_binary 'Posse');
/*!40000 ALTER TABLE `enderecos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `itens_pedido`
--

DROP TABLE IF EXISTS `itens_pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `itens_pedido` (
  `id_item_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` double NOT NULL,
  `total` double NOT NULL,
  `entrega` varchar(45) DEFAULT NULL,
  `id_pedido` int(11) DEFAULT NULL,
  `id_produto` int(11) DEFAULT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `tamanho` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_item_pedido`),
  KEY `id_pedido` (`id_pedido`),
  KEY `id_produto` (`id_produto`),
  KEY `itens_pedido_ibfk3` (`id_cliente`),
  CONSTRAINT `itens_pedido_ibfk3` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `itens_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`),
  CONSTRAINT `itens_pedido_ibfk_2` FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id_produto`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itens_pedido`
--

LOCK TABLES `itens_pedido` WRITE;
/*!40000 ALTER TABLE `itens_pedido` DISABLE KEYS */;
INSERT INTO `itens_pedido` VALUES (93,1,64.99,64.99,'casa',46,1,24,'g'),(94,1,49.99,49.99,'local',47,1,19,'m');
/*!40000 ALTER TABLE `itens_pedido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `nome_cliente` varchar(100) NOT NULL,
  `tipo_pedido` varchar(100) NOT NULL,
  `status_pedido` varchar(100) NOT NULL,
  `data_pedido` datetime NOT NULL,
  `valor_total` double NOT NULL,
  `id_cliente` int(11) DEFAULT NULL,
  `nota` int(11) DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  PRIMARY KEY (`id_pedido`),
  KEY `id_cliente` (`id_cliente`),
  CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidos`
--

LOCK TABLES `pedidos` WRITE;
/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
INSERT INTO `pedidos` VALUES (46,'Paulo','casa','Recebido','2025-06-13 20:05:55',49.99,24,NULL,'Sem Comentários'),(47,'Ricka','local','Recebido','2025-06-13 20:12:23',49.99,19,NULL,'Sem Comentários');
/*!40000 ALTER TABLE `pedidos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produtos`
--

DROP TABLE IF EXISTS `produtos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produtos` (
  `id_produto` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `preco` double NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `descricao_resumida` varchar(100) DEFAULT NULL,
  `dadosPagina` varchar(100) DEFAULT NULL,
  `tipo` varchar(100) DEFAULT NULL,
  `sabor` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_produto`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produtos`
--

LOCK TABLES `produtos` WRITE;
/*!40000 ALTER TABLE `produtos` DISABLE KEYS */;
INSERT INTO `produtos` VALUES (1,'Pizza de Calabresa',49.99,'..\\IMG\\Produtos\\Calabresa.png','Pizza clássica com calabresa.','..\\Json\\Calabresa.json','normal','trad'),(11,'Quattro Formaggi',49.99,'../IMG/Produtos/produto_684c9903e62418.78399586.jpg','Uma explosão de queijos.','..\\Json\\quatroQueijo.json','normal','trad'),(12,'Araba Delizia',46.9,'../IMG/Produtos/produto_684c99a121be93.03759820.jpg','Tempero árabe tradicional.','../Json/arabe.json','normal','esp'),(13,'Banana alla Cannella ',38.9,'../IMG/Produtos/produto_684c9a1538ee12.21606531.jpg','Perfeita para sobremesa.','../Json/Banana.json','normal','doce'),(14,'Banana al Cioccolato',42.9,'../IMG/Produtos/produto_684c9a6dd749c5.87069394.jpg','Sobremesa quente e indulgente.','../Json/Banana-Chocolate.json','normal','doce'),(15,'Broccoli Verdezza',44.9,'../IMG/Produtos/produto_684c9ab66f98d7.26685793.jpg','Leve e saborosa.','../Json/Brocolis.json','normal','esp'),(16,'Gamberetti di Mare',62.9,'../IMG/Produtos/produto_684c9b1c6663f0.17950265.jpg','Um toque sofisticado dos mares','../Json/Camarao.json','normal','esp'),(17,'Carne Secca Rustica',56.9,'../IMG/Produtos/produto_684c9b6d7aeb21.23940515.jpg','Um clássico do sertão.','../Json/Carne.json','normal','trad'),(18,'Funghi Classico',52.9,'../IMG/Produtos/produto_684c9ba4457b63.94814471.jpg','Para os amantes de cogumelos.','../Json/Cogumelojson','normal','esp'),(19,'Pollo Catupiry',47.9,'../IMG/Produtos/produto_684c9bd8c0de78.41601648.jpg','Combinação clássica e cremosa.','../Json/produto_684c9bd8c11282.14024125.json','normal','trad'),(20,'Casa della Nonna',54.9,'../IMG/Produtos/produto_684c9c36c72cd8.14111795.jpg','Um mix especial da casa','../Json/produto_684c9c36c7d347.31348703.json','normal','trad'),(21,'Fragola Dolce',44.9,'../IMG/Produtos/produto_684c9c79384423.91929738.jpg','Pizza doce com morangos fresco','../Json/produto_684c9c793992c0.13805457.json','normal','doce'),(22,'Fragola Speciale',49.9,'../IMG/Produtos/produto_684c9cbd6bdd40.41462479.jpg','Uma sobremesa refinada.','../Json/produto_684c9cbd6cc404.69924698.json','normal','esp'),(23,'Napoletana Originale',45.9,'../IMG/Produtos/produto_684c9d032b4eb4.59456197.jpg','A tradicional napolitana.','../Json/produto_684c9d032ba785.62674932.json','normal','trad'),(24,'Salmone Affumicato',69.9,'../IMG/Produtos/produto_684c9d33750dc2.47404228.jpg','Sabor refinado e marcante.','../Json/produto_684c9d3375bf47.12524145.json','normal','esp'),(25,'Combo Família',59.9,'../IMG\\Produtos\\ComboFamilia.jpg','O combo perfeito para a Familia','../Json\\ComboFamilia.json','combo',NULL);
/*!40000 ALTER TABLE `produtos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-21 14:58:45
