-- Song Request Queue Database Schema
-- Import: Using XAMPP, phpMyAdmin go to Import tab (no database selected) to import this file


-- Create DB song_queue, so just import to mysql

CREATE DATABASE IF NOT EXISTS song_queue
  CHARACTER SET utf8mb4 -- Modern Standard: MySQL 8.0 (and newer) switched utf8mb4 as the default character set.
  COLLATE utf8mb4_unicode_ci;


USE song_queue;


-- One row per requested/submitted song
CREATE TABLE IF NOT EXISTS songs (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title       VARCHAR(80)  NOT NULL,
  artist      VARCHAR(60)  NOT NULL DEFAULT '', -- Optional, so default ''
  -- open = in the queue, answered = handled by the DJ
  -- deleted = hidden by the moderator (spam or offensive)
  status      ENUM('open', 'answered', 'deleted') NOT NULL DEFAULT 'open',
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- One row per vote; a session token can vote once per song
CREATE TABLE IF NOT EXISTS votes (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  song_id      INT UNSIGNED NOT NULL, -- pkey from songs table
  voter_token  VARCHAR(64)  NOT NULL,
  created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY one_vote_per_song (song_id, voter_token), -- Same token can't vote twice on a single song
  CONSTRAINT fk_votes_song
    FOREIGN KEY (song_id) REFERENCES songs (id)
    ON DELETE CASCADE -- When a song is deleted from db, delete from votes table
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Testing db songs
INSERT INTO songs (title, artist) VALUES
  ('Test Song A', 'Artist A'),
  ('Test Song B', 'Artist B'),
  ('Test Song C', 'Artist C');

-- Testing db votes
INSERT INTO votes (song_id, voter_token) VALUES
  (1, 'test-token-1'),
  (1, 'test-token-2'),
  (2, 'test-token-1');