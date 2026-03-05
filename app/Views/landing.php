<?php use App\Core\Helpers; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Global Styles -->
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/theme.css') ?>">
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/global.css') ?>">
    <!-- Page Specific Styles -->
    <link rel="stylesheet" href="<?= Helpers::baseUrl('../assets/css/index.css') ?>">
    <title>Student Placement and Attachment System - CUEA </title>
</head>
<body>
    <div class="Landing-Page-Container">
  <div class="container">
    <div class="background-shadow">
      <div class="container2">
        <div class="margin">
          <div class="container3">
            <div class="img-university-logo-margin">
              <img class="university-logo" src="<?= Helpers::baseUrl('../assets/cuea-logo.png') ?>" />
            </div>
            <div class="container4">
              <div class="image-text">
                The Catholic University of Eastern Africa
              </div>
            </div>
          </div>
        </div>
        <div class="heading-1-margin">
          <div class="heading-1">
            <div class="attachment-management-system">
              CUEA Attachment
              <br />
              Management System
            </div>
          </div>
        </div>
        <div class="margin2">
          <div class="container5">
            <div
              class="Information-text"
            >
              Managing the CUEA Industrial Attachments and Student Placement Process Effectively and Efficiently.
            </div>
          </div>
        </div>
        <div class="container6">
          <div class="student-login">
            <a href="<?= Helpers::baseUrl('/login/student') ?>">
              <button class="button">Student Login</button>
            </a>
          </div>
          <div class="staff-login">
            <a href="<?= Helpers::baseUrl('/login/staff') ?>">
              <button class="button2">Staff Login</button>
            </a>
          </div>
          <div class="host-organization-login">
            <a href="<?= Helpers::baseUrl('/login/host') ?>">
              <button class="button3">Host Organization Login</button>
            </a>
          </div>
        </div>
      </div>
      <img
        class="background-image"
        src="<?= Helpers::baseUrl('../assets/CUEA_Ext-01.jpg') ?>"
      />
    </div>
  </div>
</div>

</body>
</html>