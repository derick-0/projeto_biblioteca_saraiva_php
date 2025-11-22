-- MySQL schema for Biblioteca Saraiva
CREATE DATABASE IF NOT EXISTS biblioteca_saraiva CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE biblioteca_saraiva;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255),
  email VARCHAR(255) UNIQUE,
  senha_hash VARCHAR(255),
  role VARCHAR(50) DEFAULT 'user',
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS autores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255),
  biografia TEXT,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) UNIQUE
);

CREATE TABLE IF NOT EXISTS livros (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255),
  autor_id INT,
  categoria_id INT,
  isbn VARCHAR(50),
  ano VARCHAR(10),
  quantidade INT DEFAULT 1,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (autor_id) REFERENCES autores(id) ON DELETE SET NULL,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS emprestimos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  livro_id INT,
  usuario_id INT,
  data_saida DATE,
  data_prevista DATE,
  data_devolucao DATE NULL,
  status VARCHAR(50),
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (livro_id) REFERENCES livros(id) ON DELETE CASCADE,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reservas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  livro_id INT,
  usuario_id INT,
  data_reserva DATE,
  status VARCHAR(50),
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (livro_id) REFERENCES livros(id) ON DELETE CASCADE,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- seed basic data
INSERT IGNORE INTO usuarios (nome,email,senha_hash,role) VALUES
('Admin','admin@saraiva.local', '', 'admin');

INSERT IGNORE INTO autores (nome) VALUES ('Machado de Assis'),('Clarice Lispector'),('Jorge Amado');
INSERT IGNORE INTO categorias (nome) VALUES ('Ficção'),('Biografia'),('Tecnologia');
INSERT IGNORE INTO livros (titulo,autor_id,categoria_id,isbn,ano,quantidade) VALUES
('Dom Casmurro',1,1,'978-XXXX',1899,3),
('A Hora da Estrela',2,1,'978-YYYY',1977,2);
