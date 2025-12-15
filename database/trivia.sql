CREATE DATABASE IF NOT EXISTS trivias_utp CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE trivias_utp;

-- Usuarios (player/admin/operator)
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(120) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL,
  nickname VARCHAR(60) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','operator','player') NOT NULL DEFAULT 'player',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Temas
CREATE TABLE topics (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE
);

-- Niveles (bloqueo por sort_order)
CREATE TABLE levels (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(60) NOT NULL UNIQUE,
  sort_order INT NOT NULL UNIQUE
);

INSERT INTO levels(name, sort_order) VALUES
('Principiante', 1),
('Novato', 2),
('Experto', 3);

-- Preguntas
CREATE TABLE questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  topic_id INT NOT NULL,
  level_id INT NOT NULL,
  type ENUM('mcq','tf') NOT NULL DEFAULT 'mcq',
  question_text VARCHAR(255) NOT NULL,
  option_a VARCHAR(255) NULL,
  option_b VARCHAR(255) NULL,
  option_c VARCHAR(255) NULL,
  option_d VARCHAR(255) NULL,
  correct_answer VARCHAR(2) NOT NULL,
  points INT NOT NULL DEFAULT 10,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE,
  FOREIGN KEY (level_id) REFERENCES levels(id) ON DELETE RESTRICT
);

-- Avatars (activo sin borrar)
CREATE TABLE avatars (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  filename VARCHAR(140) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Premios
CREATE TABLE prizes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  topic_id INT NOT NULL,
  level_id INT NOT NULL,
  title VARCHAR(120) NOT NULL,
  image VARCHAR(140) NOT NULL,
  points_required INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE,
  FOREIGN KEY (level_id) REFERENCES levels(id) ON DELETE RESTRICT
);

-- Progreso por usuario + tema + nivel (unique)
CREATE TABLE progress (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  topic_id INT NOT NULL,
  level_id INT NOT NULL,
  total_points INT NOT NULL DEFAULT 0,
  percent_complete DECIMAL(5,2) NOT NULL DEFAULT 0,
  last_level_at DATETIME NULL,
  UNIQUE KEY uq_progress(user_id, topic_id, level_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE,
  FOREIGN KEY (level_id) REFERENCES levels(id) ON DELETE RESTRICT
);

-- Sets de preguntas (QR) + multi-jugador
CREATE TABLE question_sets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(20) NOT NULL UNIQUE,
  topic_id INT NOT NULL,
  level_id INT NOT NULL,
  created_by INT NOT NULL,
  limit_questions INT NOT NULL DEFAULT 10,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (topic_id) REFERENCES topics(id),
  FOREIGN KEY (level_id) REFERENCES levels(id),
  FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Log de respuestas + tiempos
CREATE TABLE answer_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  question_id INT NOT NULL,
  question_set_id INT NULL,
  answer_given VARCHAR(2) NOT NULL,
  is_correct TINYINT(1) NOT NULL DEFAULT 0,
  seconds_to_answer DECIMAL(10,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
  FOREIGN KEY (question_set_id) REFERENCES question_sets(id) ON DELETE SET NULL
);

-- Temas base solicitados
INSERT INTO topics(name) VALUES ('PHP'), ('Javascript'), ('Laravel');
