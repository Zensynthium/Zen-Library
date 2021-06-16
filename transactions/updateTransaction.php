<?php
// Include config file
require_once "../config.php";

// Retrieving Books, Members, and Employees for Verification

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

// Define variables and initialize with empty values
$bookID = $memberID = $employeeID = $transDate = $isCheckOut = "";
$bookID_err = $memberID_err = $employeeID_err = $transDate_err = $isCheckOut_err = "";
 
// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Get hidden input value
    $id = $_POST["id"];

    // Validate bookID
    $input_bookID = trim($_POST["bookID"]);
    if(empty($input_bookID)){
      $bookID_err = "Please enter a Book ID.";
    } elseif(intval($input_bookID) > count($bookArray)){
      $bookID_err = "Invalid Book ID";
    }else {
      $input_bookID = filter_var($input_bookID, FILTER_SANITIZE_STRING);
      $bookID = $input_bookID;
    }

    // Validate employeeID
    $input_employeeID = trim($_POST["employeeID"]);
    if(empty($input_employeeID)){
      $employeeID_err = "Please enter an Employee ID.";     
    } elseif(intval($input_employeeID) > count($employeeArray)){
      $employeeID_err = "Invalid Employee ID";
    } else {
      $input_employeeID = filter_var($input_employeeID, FILTER_SANITIZE_STRING);
      $employeeID = $input_employeeID;
    }

    // Validate transDate
    $input_transDate = trim($_POST["transDate"]);
    $date_array = explode("-", $input_transDate);
    $curr_date = new DateTime();
    $transDate_date = new DateTime($input_transDate);

    if(empty($input_transDate)){
      $transDate_err = "Please enter a transaction date.";     
    } elseif (!checkdate(ltrim($date_array[1], "0"), ltrim($date_array[2], "0"), $date_array[0])) {
      $transDate_err = "Please enter a valid date.";
    } elseif ($transDate_date > $curr_date){
      $transDate_err = "Please enter a transaction date that is the present or in the past.";  
    } else {
      $transDate = $input_transDate;
    }

    // Validate isCheckOut
    $input_isCheckOut = trim($_POST["isCheckOut"]);

    $availability = $bookArray[$input_bookID-1]["Availability"];

    if(!isset($input_isCheckOut)){
      $isCheckOut_err = "Please enter an action (Check Out or Check In).";     
    } else {
      $isCheckOut = $input_isCheckOut;
    }

    // Validate memberID 
    // (Validated after isCheckOut because books can be returned anonymously through drop boxes)
    $input_memberID = trim($_POST["memberID"]);
    // $bookIsCheckOut = $bookArray[$input_bookID-1]["isCheckOut"];
    if(empty($input_memberID) && $isCheckOut == "1"){
      $memberID_err = "Please enter a Member ID (Required for Check Outs).";     
    } elseif(intval($input_memberID) > count($memberArray)){
      $memberID_err = "Invalid Member ID";
    }else{
      $input_memberID = filter_var($input_memberID, FILTER_SANITIZE_STRING);
      $memberID = $input_memberID;
    }


    // Check input errors before inserting in database
    if(empty($bookID_err) && empty($transDate_err) && empty($memberID_err) && empty($isCheckOut_err) && empty($employeeID_err)){
        // Prepare an insert statement
        $sql = "UPDATE Transactions SET BookID=?, MemberID=?, EmployeeID=?, TransDate=?, IsCheckOut=? WHERE TransID=?";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "iiissi", $param_bookID, $param_memberID, $param_employeeID, $param_transDate, $param_isCheckOut, $param_id);
            
            // Set parameters
            $param_bookID = $bookID;
            $param_memberID = $memberID;
            $param_employeeID = $employeeID;
            $param_transDate = $transDate;
            $param_isCheckOut = $isCheckOut;
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            mysqli_stmt_execute($stmt);
        } else{
          echo "Something went wrong. Please try again later.";
        }
        
        // Check existence of id parameter before processing further
        if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
          // Get URL parameter
          $id = trim($_GET["id"]);

        // Prepare an update statement
        $sql = "UPDATE Books SET Availability = ? WHERE id = ?";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ii", $param_isCheckOut, $param_bookID);

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to Transactions page
                header("location: index.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
      }
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($conn);
    } else {
      // Check existence of id parameter before processing further
      if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
          // Get URL parameter
          $id = trim($_GET["id"]);
    
          // Prepare a select statement
          $sql = "SELECT * FROM Transactions WHERE TransID = ?";
          if($stmt = mysqli_prepare($conn, $sql)){
              
          // Bind variables to the prepared statement as parameters
          mysqli_stmt_bind_param($stmt, "i", $param_id);
          
          // Set parameters
          $param_id = $id;

              // Attempt to execute the prepared statement
              if(mysqli_stmt_execute($stmt)){
                  $result = mysqli_stmt_get_result($stmt);
      
                  if(mysqli_num_rows($result) == 1) {
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $bookID = $row["BookID"];
                    $memberID = $row["MemberID"];
                    $employeeID = $row["EmployeeID"];
                    $transDate = $row["TransDate"];
                    $isCheckOut = $row["IsCheckOut"];
                  } else {
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                  }
              } else{
                echo "Oops! Something went wrong. Please try again later.";
              }
          }
        
        // Close statement
        mysqli_stmt_close($stmt);
        
        // Close connection
        mysqli_close($conn);
      } else {
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
      }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
  <title>Update Transaction</title>
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

  <div class="wrapper">
    <div class="container-fluid">
      <div class="row">
          <div class="col-md-12">
            <div class="page-header">
              <h2 class="my-2">Update Transaction</h2>
            </div>
              <p>Please fill this form and submit to add a Transaction into the database.</p>
              <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                  <div class="form-group <?php echo (!empty($bookID_err)) ? 'has-error' : ''; ?>">
                      <label>Book ID</label>
                      <select id="bookID" name="bookID" class="form-control">
                        <!-- <option value="" selected disabled hidden></option> -->
                        <?php 
                          if ($bookArray){
                            for ($i = 0; $i < count($bookArray); $i++) {
                              echo '<option ' . ($bookArray[$i]['ID'] == $bookID ? 'selected': '') .' value="'. $bookArray[$i]['ID'] .'">'. $bookArray[$i]['ID'] . ' - '. $bookArray[$i]['Title'] . ' ('. (!$bookArray[$i]['Availability'] ? 'Checked In' : 'Checked Out')  .')</option>';
                            }
                          }
                        ?>
                      </select>
                      <span class="help-block text-danger"><?php echo $bookID_err;?></span>
                  </div>
                  <div class="form-group <?php echo (!empty($memberID_err)) ? 'has-error' : ''; ?>">
                      <label>Member ID</label>
                      <select name="memberID" class="form-control">
                        <!-- <option value="" selected disabled hidden></option> -->
                        <?php 
                          if ($memberArray){
                            for ($i = 0; $i < count($memberArray); $i++) {
                              echo '<option ' . ($memberArray[$i]['ID'] == $memberID ? 'selected': '') .' value="'. $memberArray[$i]['ID'] .'">'. $memberArray[$i]['ID'] . ' - '. $memberArray[$i]['Name'] .'</option>';
                            }
                          }
                        ?>
                      </select>
                      <span class="help-block text-danger"><?php echo $memberID_err;?></span>
                  </div>
                  <div class="form-group <?php echo (!empty($employeeID_err)) ? 'has-error' : ''; ?>">
                      <label>Employee ID</label>
                      <select name="employeeID" class="form-control">
                        <!-- <option value="" selected disabled hidden></option> -->
                        <?php 
                          if ($employeeArray){
                            for ($i = 0; $i < count($employeeArray); $i++) {
                              echo '<option ' . ($employeeArray[$i]['ID'] == $employeeID ? 'selected': '') .' value="'. $employeeArray[$i]['ID'] .'">'. $employeeArray[$i]['ID'] . ' - '. $employeeArray[$i]['Name'] .'</option>';
                            }
                          }
                        ?>
                      </select>
                      <span class="help-block text-danger"><?php echo $employeeID_err;?></span>
                  </div>
                  <div class="form-group <?php echo (!empty($transDate_err)) ? 'has-error' : ''; ?>">
                      <label>Transaction Date</label>
                      <input type="date" name="transDate" class="form-control" value="<?php echo $transDate; ?>">
                      <span class="help-block text-danger"><?php echo $transDate_err;?></span>
                  </div>
                  <div class="form-group <?php echo (!empty($isCheckOut_err)) ? 'has-error' : ''; ?>">
                      <label>Checking Out or Returning?</label>
                      <select name="isCheckOut" class="form-control">
                        <!-- <option value="" disabled hidden></option> -->
                        <option <?php ($isCheckOut == '1' ? 'selected' : ''); ?> value="1">Checking Out Book</option>
                        <option <?php ($isCheckOut == '0' ? 'selected' : ''); ?> value="0">Returning Book</option>
                      </select>
                      <span class="help-block text-danger"><?php echo $isCheckOut_err;?></span>
                  </div>
                  <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                  <input type="submit" class="btn btn-primary mt-2" value="Submit">
                  <a href="index.php" class="btn btn-default mt-2">Cancel</a>
              </form>
          </div>
      </div>        
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
</body>
</html>