<style>
  .divider {
    border: none;
    border-top: 1px dotted rgba(0, 0, 0, 0.1);
    margin: 10px 0;
  }
</style>    
      
      <!-- partial -->
      <!-- partial:../../partials/_sidebar.html -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">

          <li class="nav-item">
            <a class="nav-link" href="index.php">
              <i class="icon-grid menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>

            <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#admissions" aria-expanded="false" aria-controls="admissions">
              <i class="fas fa-user-graduate menu-icon"></i>
              <span class="menu-title">Admissions</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="admissions">
              <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="admission/student_list.php">Student Records</a></li>
              <li class="nav-item"> <a class="nav-link" href="admission/guardian_list.php">Guardian Records</a></li>
              </ul>
            </div>
          </li>


        <?php if (!empty($allowedModules['User Management']['can_view'])) : ?>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#users" aria-expanded="false" aria-controls="users">
              <i class="fas fa-user-cog menu-icon"></i>
              <span class="menu-title">User Mgmt</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="users">
              <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="users/user_management.php">Add Users</a></li>
              </ul>
            </div>
          </li>
        <?php endif; ?>


        <?php if (!empty($allowedModules['User Management']['can_view'])) : ?>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#detail_setting" aria-expanded="false" aria-controls="detail_setting">
              <i class="fas fa-cog menu-icon"></i>
              <span class="menu-title">Detail Setting</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="detail_setting">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link" href="setting_lms/blood_group.php">Blood Group</a></li>
                <li class="nav-item"> <a class="nav-link" href="setting_lms/class.php">Class</a></li>
                <li class="nav-item"> <a class="nav-link" href="setting_lms/fee_package.php">Fee Package</a></li>
                <li class="nav-item"> <a class="nav-link" href="setting_lms/gender.php">Gender</a></li>
                <li class="nav-item"> <a class="nav-link" href="setting_lms/group.php">Group</a></li>
                <li class="nav-item"> <a class="nav-link" href="setting_lms/mobile_operator.php">Mobile Operator</a></li>
                <li class="nav-item"> <a class="nav-link" href="setting_lms/religion.php">Religion</a></li>
                <li class="nav-item"> <a class="nav-link" href="setting_lms/section.php">Section</a></li>
                <li class="nav-item"> <a class="nav-link" href="setting_lms/student_category.php">Student Category</a></li>
                <hr class="divider">
                <li class="nav-item"> <a class="nav-link" href="setting_lms/country.php">Country</a></li>
                <li class="nav-item"> <a class="nav-link" href="setting_lms/state.php">State</a></li>
                <li class="nav-item"> <a class="nav-link" href="setting_lms/city.php">City</a></li>
                <li class="nav-item"> <a class="nav-link" href="setting_lms/area.php">Area</a></li>
              </ul>
            </div>
          </li>
        <?php endif; ?>


          <?php if (!empty($allowedModules['User Management']['can_view'])) : ?>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#tables" aria-expanded="false" aria-controls="tables">
              <i class="fas fa-cog menu-icon"></i>
              <span class="menu-title">Account Setting</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="tables">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link" href="setting/login.php">Password</a></li>
                <li class="nav-item"> <a class="nav-link" href="setting/general.php">Basic Information</a></li>
                <li class="nav-item"> <a class="nav-link" href="setting/images.php">Logo | Images</a></li>
                <li class="nav-item"> <a class="nav-link" href="setting/download_backup.php">Backup</a></li>
              </ul>
            </div>
          </li>
        <?php endif; ?>

        </ul>
      </nav>