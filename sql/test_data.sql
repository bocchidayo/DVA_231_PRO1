-- Insert test data to db
-- Tested: Same token can't vote in the same song twice.

USE song_queue;

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