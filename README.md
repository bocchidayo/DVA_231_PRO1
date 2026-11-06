# DVA_231_PRO1
Collaborative Queue and Upvote Board

# Task - Develop an anonymous song-request queue for a live event.
Users submit items and upvote existing ones (restricted to one vote per item using a session token or hashed IP stored in the database), 
while an AJAX loop dynamically re-orders the DOM list using CSS transitions whenever rankings change. 
A passkey-protected moderator view adds a button to each item that allows marking items as "answered" or delete spam. 
Passkey can be hardcoded in the application.


# Folder Structure
Maintain a clean root folder with only required php files and README.md
- includes/ - Files to include: db.php
- styles/ - CSS
- js/ - Javascript
- mysql/ - SQL