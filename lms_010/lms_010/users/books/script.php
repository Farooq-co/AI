<script>
document.getElementById('addBookForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  var name = document.getElementById('name').value;
  var status = document.getElementById('status').value;
  
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'books/add.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  
  xhr.onload = function() {
    if (this.status == 200) {
      location.reload();
    } else {
      alert('An error occurred while adding the book.');
    }
  };
  
  xhr.send('name=' + encodeURIComponent(name) + '&status=' + encodeURIComponent(status));
});

function editBook(id) {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', 'books/get.php?id=' + id, true);
  
  xhr.onload = function() {
    if (this.status == 200) {
      var bookData = JSON.parse(this.responseText);
      
      // Populate the form fields with the retrieved data
      document.getElementById('editBookId').value = bookData.id; // Hidden input for the ID
      document.getElementById('editName').value = bookData.name;
      document.getElementById('editStatus').value = bookData.status;
      
      // Show the modal
      $('#editBookModal').modal('show');
    } else {
      alert('An error occurred while fetching book details.');
    }
  };
  
  xhr.send();
}

document.getElementById('editBookForm').addEventListener('submit', function(e) {
  e.preventDefault();

  var id = document.getElementById('editBookId').value;
  var name = document.getElementById('editName').value;
  var status = document.getElementById('editStatus').value;

  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'books/edit.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function() {
    if (this.status == 200) {
      location.reload();
    } else {
      alert('An error occurred while editing the book.');
    }
  };

  xhr.send('editBookId=' + encodeURIComponent(id) + '&editName=' + encodeURIComponent(name) + '&editStatus=' + encodeURIComponent(status));
});

function updateStatus(id, status) {
  if (confirm('Are you sure you want to update the status?')) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'books/update_status.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
      if (this.status == 200) {
        location.reload();
      } else {
        alert('An error occurred while updating the book status.');
      }
    };
    
    xhr.send('id=' + encodeURIComponent(id) + '&status=' + encodeURIComponent(status));
  }
}

function markAsActive(id) {
  updateStatus(id, 'Active');
}

function markAsInactive(id) {
  updateStatus(id, 'Inactive');
}

// Function to handle the "Delete" action
function deleteBook(id) {
  updateStatus(id, 'Deleted');
}
</script>
