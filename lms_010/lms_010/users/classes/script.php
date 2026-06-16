<script>
document.getElementById('addClassForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  var name = document.getElementById('name').value;
  var status = document.getElementById('status').value;
  
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'classes/add.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  
  xhr.onload = function() {
    if (this.status == 200) {
      location.reload();
    } else {
      alert('An error occurred while adding the class.');
    }
  };
  
  xhr.send('name=' + encodeURIComponent(name) + '&status=' + encodeURIComponent(status));
});

function editClass(id) {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', 'classes/get.php?id=' + id, true);
  
  xhr.onload = function() {
    if (this.status == 200) {
      var classData = JSON.parse(this.responseText);
      
      // Populate the form fields with the retrieved data
      document.getElementById('editClassId').value = classData.id; // Hidden input for the ID
      document.getElementById('editName').value = classData.name;
      document.getElementById('editStatus').value = classData.status;
      
      // Show the modal
      $('#editClassModal').modal('show');
    } else {
      alert('An error occurred while fetching class details.');
    }
  };
  
  xhr.send();
}

document.getElementById('editClassForm').addEventListener('submit', function(e) {
  e.preventDefault();

  var id = document.getElementById('editClassId').value;
  var name = document.getElementById('editName').value;
  var status = document.getElementById('editStatus').value;

  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'classes/edit.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function() {
    if (this.status == 200) {
      location.reload();
    } else {
      alert('An error occurred while editing the class.');
    }
  };

  xhr.send('editClassId=' + encodeURIComponent(id) + '&editName=' + encodeURIComponent(name) + '&editStatus=' + encodeURIComponent(status));
});

function updateStatus(id, status) {
  if (confirm('Are you sure you want to update the status?')) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'classes/update_status.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
      if (this.status == 200) {
        location.reload();
      } else {
        alert('An error occurred while updating the class status.');
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
function deleteClass(id) {
  updateStatus(id, 'Deleted');
}
</script>
