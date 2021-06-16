<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
  <title>Library Application</title>
  <style>
    .img-container {
      width: 70%;
    }
  </style>
</head>
<body class="text-center">
  <?php 
  // Include config file
  require_once "config.php";

  ?>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Library Application</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./books/index.php">Books</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./employees/index.php">Employees</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./members/index.php">Members</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./transactions/index.php">Transactions</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <h1 class="p-2 mt-2">Library Application</h1>
  <p>Welcome to the Library Application! Choose your destination with the navigation bar.</p>
  <div class="my-4 img-container mx-auto">
    <img class="rounded img-fluid shadow-lg" src="images/library.jpg" alt="Library">
  </div>  
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
</body>
</html>