<!DOCTYPE html>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Library System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
  <style>
    .link-button {
      text-decoration: none;
    }

    .action-icon {
      width: 20px;
      height: 20px;
    }
  </style>
</head>
<body>
  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark text-center">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Library Application</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="../index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../books/index.php">Books</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../employees/index.php">Employees</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../members/index.php">Members</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="index.php">Transactions</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

<h1 class="text-center my-3">Library Application</h1>
<h2 class="text-center"><u>Transactions</u></h2>
  <?php 
    // connect to the database
    require_once "../config.php";
      // Prepare a select statement for relevant tables
  $sql = "SELECT ID, Title, Availability FROM Books;
  SELECT ID, Name FROM Members;
  SELECT ID, Name FROM Employees;";

  if ($conn->multi_query($sql)){
    if ($result = $conn->store_result()) {
      $bookArray = $result->fetch_all(MYSQLI_ASSOC);
      $result->free();
    }

    $conn->next_result();

    if ($result = $conn->store_result()) {
      $memberArray = $result->fetch_all(MYSQLI_ASSOC);
      $result->free();
    }

    $conn->next_result();

    if ($result = $conn->store_result()) {
      $employeeArray = $result->fetch_all(MYSQLI_ASSOC);
      $result->free();
    }
  }

  $sql = "SELECT * FROM Transactions";
  if($result = mysqli_query($conn, $sql)){
    if(mysqli_num_rows($result) > 0){
        echo "<table class='table table-hover text-center'>";
            echo "<thead>";
                echo "<tr>";
                    echo "<th>#</th>";
                    echo "<th>[ID] Book</th>";
                    echo "<th>[ID] Member</th>";
                    echo "<th>[ID] Employee</th>";
                    echo "<th>Transaction Date</th>";
                    echo "<th>Action</th>";
                    echo "<th>Edit/Delete</th>";
                echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            while($row = mysqli_fetch_array($result)){
                echo "<tr>";
                    echo "<td>" . $row['TransID'] . "</td>";
                    echo "<td>[" . $row['BookID'] . "] " . $bookArray[$row['BookID']-1]['Title'] . "</td>";
                    echo "<td>[" . $row['MemberID'] . "] " . $memberArray[$row['MemberID']-1]['Name'] . "</td>";
                    echo "<td>[" . $row['EmployeeID'] . "] " . $employeeArray[$row['EmployeeID']-1]['Name'] . "</td>";
                    echo "<td>" . $row['TransDate'] . "</td>";
                    echo ($row['IsCheckOut'] == 0) ? "<td>Return</td>" : "<td>Check Out</td>";
                    echo "<td>";
                    echo "<a href='updateTransaction.php?id=". $row['TransID'] ."' title='Update Transaction'><img class='action-icon my-1 mx-2' src='../icons/edit-pencil.svg' alt=''></span></a>";
                    echo "<a href='deleteTransaction.php?id=". $row['TransID'] ."' title='Delete Transaction'><img class='action-icon my-1 mx-2' src='../icons/trash.svg' alt=''></span></a>";
                    echo "</td>";
                echo "</tr>";
            }
            echo "</tbody>";                            
        echo "</table>";
        // Free result set
        mysqli_free_result($result);
    } else{
        echo "<p class='lead text-center'><em>No records were found.</em></p>";
    }
  } else{
      echo "ERROR: Could not execute $sql. " . mysqli_error($conn);
  }
    mysqli_close($conn);
  ?>
  <a class="link-button" href="addTransaction.php"><button class="mt-2 d-block mx-auto">Add Transactions</button></a>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
</body>
</html>

