<script>
document.addEventListener('DOMContentLoaded', function() {
    changeEntriesPerPage(entriesPerPage); // Initialize entries per page
    document.getElementById('searchInput').addEventListener('input', searchTable); // Add event listener for live search
    paginateTable(); // Initial pagination
    updatePagination(); // Initialize pagination display
});

// Global variables
let currentPage = 1;
let entriesPerPage = 20;
let sortOrder = {}; // Store sort order for each column
const pagination = document.getElementById('pagination');
const pageNumbers = document.getElementById('pageNumbers');

// Form submission and director management
document.getElementById('addDirectorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'directors/add_director.php', true);
    xhr.onload = function() {
        if (this.status == 200) location.reload();
        else alert('An error occurred while adding the director.');
    };
    xhr.send(formData);
});

function editDirector(name) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'directors/get_director.php?name=' + name, true);
    xhr.onload = function() {
        if (this.status == 200) {
            var directorData = JSON.parse(this.responseText);
            document.getElementById('editDirectorId').value = directorData.name;
            document.getElementById('editName').value = directorData.name;
            document.getElementById('editPobox').value = directorData.pobox;
            document.getElementById('editTel').value = directorData.tel;
            document.getElementById('editKrapin').value = directorData.krapin;
            document.getElementById('editNok').value = directorData.nok;
            document.getElementById('editStatus').value = directorData.status;
            $('#editDirectorModal').modal('show');
        } else alert('An error occurred while fetching director details.');
    };
    xhr.send();
}

document.getElementById('editDirectorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'directors/edit_director.php', true);
    xhr.onload = function() {
        if (this.status == 200) location.reload();
        else alert('An error occurred while editing the director.');
    };
    xhr.send(formData);
});

function activateDirector(name) {
    if (confirm('Are you sure you want to mark this director as active?')) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'directors/update_director_status.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (this.status == 200) location.reload();
            else alert('An error occurred while updating the director status.');
        };
        xhr.send('name=' + name + '&status=active');
    }
}

function deactivateDirector(name) {
    if (confirm('Are you sure you want to mark this director as inactive?')) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'directors/update_director_status.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (this.status == 200) location.reload();
            else alert('An error occurred while updating the director status.');
        };
        xhr.send('name=' + name + '&status=inactive');
    }
}

// Search functionality
function searchTable() {
    var input, filter, table, tr, td, i, j, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toLowerCase();
    table = document.querySelector("table");
    tr = table.getElementsByTagName("tr");
    for (i = 1; i < tr.length; i++) {
        tr[i].style.display = "none";
        td = tr[i].getElementsByTagName("td");
        for (j = 0; j < td.length; j++) {
            if (td[j]) {
                txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                    break;
                }
            }
        }
    }
}

// Handle changing entries per page
function changeEntriesPerPage(entries) {
    entriesPerPage = entries;
    currentPage = 1;
    paginateTable();
    updatePagination();
}

// Paginate the table
function paginateTable() {
    var table, tr, i;
    table = document.querySelector("table");
    tr = table.getElementsByTagName("tr");

    // Hide all rows
    for (i = 1; i < tr.length; i++) {
        tr[i].style.display = "none";
    }

    // Show rows for the current page
    var start = (currentPage - 1) * entriesPerPage + 1;
    var end = start + entriesPerPage;
    for (i = start; i < end && i < tr.length; i++) {
        tr[i].style.display = "";
    }
}

// Update pagination
function updatePagination() {
    var table, tr;
    table = document.querySelector("table");
    tr = table.getElementsByTagName("tr");

    // Clear previous page numbers
    pageNumbers.innerHTML = '';

    // Calculate the number of pages
    var totalPages = Math.ceil((tr.length - 1) / entriesPerPage);

    // Create page numbers
    for (let i = 1; i <= totalPages; i++) {
        const pageButton = document.createElement('button');
        pageButton.classList.add('btn', 'btn-primary');
        pageButton.textContent = i;
        pageButton.onclick = function () {
            currentPage = i;
            paginateTable();
        };
        pageNumbers.appendChild(pageButton);
    }
}

// Go to the next page
function nextPage() {
    var table, tr;
    table = document.querySelector("table");
    tr = table.getElementsByTagName("tr");
    var visibleRows = Array.from(tr).slice(1).filter(row => row.style.display === "" || row.style.display === "table-row").length;
    if (currentPage * entriesPerPage < visibleRows) {
        currentPage++;
        paginateTable();
        updatePagination();
    }
}

// Go to the previous page
function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        paginateTable();
        updatePagination();
    }
}

function sortTable(columnIndex) {
    const table = document.querySelector("table");
    const tbody = table.querySelector("tbody");
    const rows = Array.from(tbody.querySelectorAll("tr"));
    const header = table.querySelectorAll("th")[columnIndex];
    const isAsc = sortOrder[columnIndex] === 'asc';
    
    // Toggle sort order
    sortOrder[columnIndex] = isAsc ? 'desc' : 'asc';
    
    // Update header classes for arrow display
    table.querySelectorAll("th").forEach(th => th.classList.remove('desc', 'asc'));
    header.classList.add(sortOrder[columnIndex]);

    const sortedRows = rows.sort((a, b) => {
        const aText = a.querySelectorAll("td")[columnIndex].textContent.trim();
        const bText = b.querySelectorAll("td")[columnIndex].textContent.trim();
        
        const aNumber = parseFloat(aText.replace(/[^0-9.-]+/g,""));
        const bNumber = parseFloat(bText.replace(/[^0-9.-]+/g,""));
        
        if (!isNaN(aNumber) && !isNaN(bNumber)) {
            // Sort numbers
            return isAsc ? aNumber - bNumber : bNumber - aNumber;
        } else {
            // Sort text
            return isAsc ? aText.localeCompare(bText) : bText.localeCompare(aText);
        }
    });

    // Remove existing rows
    tbody.innerHTML = '';

    // Append sorted rows
    sortedRows.forEach(row => tbody.appendChild(row));

    paginateTable(); // Reapply pagination after sorting
    updatePagination();
}
</script>


  <!-- Custom CSS to set minimum column width -->
  <style>
    table th, table td {
      min-width: 200px;
    }
  </style>