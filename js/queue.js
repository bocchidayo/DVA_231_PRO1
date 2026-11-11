const queue = document.getElementById('queue');
const row_height = 60; // row height + gap, in pixels

let isMod = false; // default is not mod (when called by index.php)

const statusMessage = document.getElementById('status-message'); // Status message in index only

const modLabel = queue.getAttribute('data-mod'); // mod label is true in mod.php
if (modLabel === 'yes') {
  isMod = true;
}

// Sample JSON from list.php
// {"id":1,"title":"Test Song A","artist":"Artist A","vote_count":2}

// New sample JSON with voted or not per session token
// {"id":5,"title":"Test Cooldown","artist":"","vote_count":3,"voted":false}

// id
// title
// artist
// vote_count
// voted or not

// Creates a single row for a song instead of looping through all songs
function createRow(song) {

  const row = document.createElement('div');
  row.className = 'song';
  row.id = 'song-' + song.id;

  const rank = document.createElement('span');
  rank.id = 'rank-' + song.id;
  rank.className = 'rank'; // Classes needed for CSS

  const title = document.createElement('strong');
  title.textContent = song.title;
  // Doesnt need class, its the only strong so .song strong

  const artist = document.createElement('span');
  artist.textContent = song.artist;
  artist.className = 'artist';

  const votes = document.createElement('span');
  votes.id = 'votes-' + song.id;
  votes.className = 'votes';

  // Creating form for posting both vote and mod
  const form = document.createElement('form');
  form.method = 'post';

  const songId = document.createElement('input');
  songId.type = 'hidden';
  songId.name = 'song_id';
  songId.value = song.id;
  form.append(songId);

  // Mod view buttons
  if (isMod) {
    form.action = "actions/mod_action.php";
    
    const answeredButton = document.createElement('button');
    answeredButton.type = 'submit';
    answeredButton.name = 'action';
    answeredButton.value = 'answered';
    answeredButton.textContent = 'Answer';

    const deleteButton = document.createElement('button');
    deleteButton.type = 'submit';
    deleteButton.name = 'action';
    deleteButton.value = 'delete';
    deleteButton.textContent = 'Delete';

    // Ask before deleting, in case the list moved under the cursor
    deleteButton.addEventListener('click', function (event) {
      if (!confirm('Delete "' + song.title + '"?')) {
        event.preventDefault();
      }
    });

    form.append(answeredButton, deleteButton);
  
  } else {
    // Index.php buttons
    // Vote button, sent to vote.php
    form.action = 'actions/vote.php';

    const voteButton = document.createElement('button');
    voteButton.id = 'vote-' + song.id; // Assigns id for all vote buttons for CSS and JS
    voteButton.type = 'submit';
    voteButton.textContent = 'Vote';

    // Event listener to send form in background with js
    form.append(voteButton);
    form.addEventListener('submit', async function (event) {
      event.preventDefault();
      const ok = await sendForm(form);
        if (ok) {
          loadList(); // Refresh list after voting with js, unlike php where it hard refreshes the page
          statusMessage.textContent = 'Vote counted!';
        }
    });
  }

  row.append(rank, ' ', title, ' ', artist, ' ', votes, ' ', form);
  return row;
}

// Returns the vote label
function voteText(count) {
  if (count === 0) {
    return 'No votes';
  } else if (count === 1) {
    return '1 vote';
  } else {
    return count + ' votes';
  }
}

// Removes rows of songs that are no longer in the list which are answered or deleted
function removeOldRows(songs) {
  const rows = Array.from(queue.children);

  for (const row of rows) {
    let found = false;

    // Loop songs row, check if they exist
    for (const song of songs) {
      if (row.id === 'song-' + song.id) {
        found = true;
      }
    }

    // Not in the list anymore = remove the row entirely using polling in real time
    if (found === false) {
      row.remove();
    }
  }
}

// Renders list of songs by looping all songs
// Create all song rows with createRow
// Update the vote count and move the row
function renderSongs(songs) {
  let position = 0;

  for (const song of songs) {
    let row = document.getElementById('song-' + song.id);

    // New song, new row
    if (row === null) {
      row = createRow(song);
      queue.appendChild(row);
    }

    // Update the vote count
    const votes = document.getElementById('votes-' + song.id);
    votes.textContent = voteText(song.vote_count);

    // Update the vote button to disabled if voted
    const voteButton = document.getElementById('vote-' + song.id);
    if (voteButton !== null) {
      if (song.voted) {
        voteButton.disabled = true;
        voteButton.textContent = 'Voted';
      } else {
        voteButton.disabled = false;
        voteButton.textContent = 'Vote';
      }
    }

    // Update the rank number shown in the row, allowing typereaders to read position
    // Wont fix typereaders, need to update the HTML as well
    const rank = document.getElementById('rank-' + song.id);
    rank.textContent = '#' + (position + 1);

    // Updates the position of the row in the queue
    // Multiplies the position it has by row height
    // This way it moves up or down using pixels
    row.style.top = (position * row_height) + 'px'; // PENDING: CSS needed to reflect this change

    position = position + 1;
  }

    // Remove rows not used anymore when answered or deleted
    // Runs out the loop so it still runs when the list is empty
    removeOldRows(songs);

    // Absolute rows took no space, so the queue has its full height
    queue.style.height = (songs.length * row_height) + 'px';

}

// Posts any form in the background, if it succeeds = true
// If error response, uses redirect for error page
async function sendForm(form) {
  const response = await fetch(form.action, { method: 'POST', body: new FormData(form)});

  if (response.url.includes('error=')) {
    window.location.href = response.url;
    return false;
  }
  return true;
}

// Request a song in the background such that only index has this request-form, allows no hard refresh in page
const requestForm = document.getElementById('request-form');
if (requestForm !== null) {
  requestForm.addEventListener('submit', async function (event) {
    event.preventDefault();
    const ok = await sendForm(requestForm);
    if (ok) {
      requestForm.reset();
      loadList();
      statusMessage.textContent = 'Song requested!';
    }
  });
}


// Fetches list of song from list.php
// Gets JSON and runs renderSongs with it
async function loadList() {
  try {
    const response = await fetch('actions/list.php');
    if (!response.ok) throw new Error('HTTP ' + response.status);

    const songs = await response.json();
    renderSongs(songs);
  } catch (error) {
    console.log('Could not load queue: ' + error.message);
  }
}


loadList();
setInterval(loadList, 3000);