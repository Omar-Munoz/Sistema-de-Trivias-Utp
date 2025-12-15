USE trivias_utp;

-- guarda el evento de inicio/fin de nivel por usuario (para tiempo entre niveles)
CREATE TABLE IF NOT EXISTS level_runs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  topic_id INT NOT NULL,
  level_id INT NOT NULL,
  question_set_id INT NULL,
  started_at DATETIME NOT NULL,
  finished_at DATETIME NULL,
  total_seconds DECIMAL(10,2) DEFAULT 0,
  correct_count INT NOT NULL DEFAULT 0,
  total_questions INT NOT NULL DEFAULT 0,
  points_earned INT NOT NULL DEFAULT 0,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE,
  FOREIGN KEY (level_id) REFERENCES levels(id) ON DELETE RESTRICT,
  FOREIGN KEY (question_set_id) REFERENCES question_sets(id) ON DELETE SET NULL
);

-- para saber el “tiempo entre un nivel y otro”
ALTER TABLE progress
  ADD COLUMN IF NOT EXISTS first_completed_at DATETIME NULL,
  ADD COLUMN IF NOT EXISTS last_completed_at DATETIME NULL;

-- opcional: índice útil
CREATE INDEX IF NOT EXISTS idx_answerlogs_set ON answer_logs(question_set_id, user_id);


USE trivias_utp;

ALTER TABLE progress
  ADD COLUMN first_completed_at DATETIME NULL,
  ADD COLUMN last_completed_at DATETIME NULL;
