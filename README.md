# DVA_231_PRO1
Collaborative Queue and Upvote Board

# Task - Develop an anonymous song-request queue for a live event.
Users submit items and upvote existing ones (restricted to one vote per item using a session token or hashed IP stored in the database), 
while an AJAX loop dynamically re-orders the DOM list using CSS transitions whenever rankings change. 
A passkey-protected moderator view adds a button to each item that allows marking items as "answered" or delete spam. 
Passkey can be hardcoded in the application.


# Folder Structure
Maintain a clean root folder with only required php files and README.md
- includes/ - Files to include: db.php and .htaccess
- styles/ - CSS
- js/ - Javascript
- mysql/ - SQL


# Task List - Ordered by Priority
- [x] mysql/schema.sql - create database + songs and votes tables
    - [x] mysql/test_data.sql - inserts test data to songs and votes db
    - [x] Tested duplicate votes manually in phpMyAdmin

- [x] includes/db.php -  settings, hardcoded mod passkey, session_start, connection
    - [x] includes/.htaccess - prevents client from accessing secrets
    - [x] Tested db connection with test.php
    - [x] Tested db connection refusal when wrong db password

- [ ] index.php - simple layout to test list.php, submit.php, vote.php and queue.js
    - [x] Simple form for submit.php
    - [x] Tables dont work, used div rows instead, that shows songs
        - [x] Dive rows is updated from JSON from list.php and shown by AJAX queue.js
        - [ ] Simple button for vote.php inside row
    - [ ] Missing index.php?error= redirects

- [x] submit.php - validate title/artist, insert title/artist into db as song_id pkey, redirect back
    - [x] Tested normal song sent to db no issues
    - [x] Tested 30 second cooldown with timestamp
    - [x] Tested whitespaces, are rejected server side
    - [x] Tested title and artist form with more than limit, are rejected server side
    - [x] Tested emojis, utf8 works properly
    - [x] Tested XSS payloads, gets stored as plaintext in db
    - [x] Tested SQLi payloads, gets stored as plaintext in db
    - [x] Tested redirects to index.php?error=

- [x] list.php - return open songs with vote counts as JSON, must be sorted
    - [x] Tested JSON returns properly sorted and counts all votes

- [ ] queue.js - poll list.php every 3s, create/update/remove rows(songs), vote form per row
    - [x] Tested manually adding vote to song, updates and adds the vote
    - [x] Tested adding new song using form, shows new song
    - [x] Tested XSS and SQLi payloads in plaintext db, they don't run in queue.js
    - [ ] Rows moving animation missing
    - [ ] Position of rows don't change in real time as CSS is required to enforce top:
    - [ ] Haven't tested removal of songs using mod_action.php
    - [ ] Screenreaders have issues, HTML in queue doesn't refresh

- [ ] vote.php - validate song_id pkey, session id as token, insert into db +1 to vote count, redirect back
- [ ] mod.php - hardcoded passkey form; when logged in, table with Answered/Delete per song, recycle list.php etc
- [ ] mod_action.php - check session, update song, redirect back
- [ ] css styling - layout and styling