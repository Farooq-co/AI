<style>
    .modal-backdrop {
      z-index: 0;
    }

    .table-bordered tbody tr {
      line-height: 1; /* Adjust the line-height as needed */
    }

    .table-bordered tbody td {
      padding: 5px; /* Adjust the padding as needed */
    }

    .btn-group .btn-primary {
      padding: 0.7rem 1.0rem; /* Adjust the padding to reduce the size */
      font-size: 0.875rem; /* Adjust the font size if needed */
    }
    .btn-group .dropdown-menu {
      font-size: 0.875rem; /* Adjust the font size of dropdown items if needed */
    }

    .sortable {
      cursor: pointer;
    }

    .sortable:after {
      content: '\25B2'; /* Up arrow */
      float: right;
      margin-left: 5px;
    }

    .sortable.desc:after {
      content: '\25BC'; /* Down arrow */
    }
  </style>