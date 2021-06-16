<?php

// Include config file
require_once "../config.php";
 
// Define variables and initialize with empty values
$name = $address = $phone = $joinDate = $birthday = "";
$name_err = $address_err = $phone_err = $joinDate_err = $birthday_err = "";
 
// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
  // Get hidden input value
  $id = $_POST["id"];

    // Validate name
    $input_name = trim($_POST["name"]);
    if(empty($input_name)){
      $name_err = "Please enter a name.";
    } else {
      $input_name = filter_var($input_name, FILTER_SANITIZE_STRING);
      $name = $input_name;
    }
    
    // Validate address
    $input_address = trim($_POST["address"]);
    if(empty($input_address)){
      $address_err = "Please enter an address.";     
    } else {
      $input_address = filter_var($input_address, FILTER_SANITIZE_STRING);
      $address = $input_address;
    }

    // Validate phone
    $phone_regex = "/[\d]{3}[^\d]{1}[\d]{3}[^\d]{1}[\d]{4}/i";
    $input_phone = trim($_POST["phone"]);
    if(empty($input_phone)){
        $phone_err = "Please enter the phone amount.";     
    } elseif(!preg_match($phone_regex, $input_phone)){
        $phone_err = "Please enter a valid phone number. format: 111-222-3344";
    } else{
        //Changed Regex to enforce format to get rid of unneccessary code

        // $special_chars = "/[\D\s\._\-]+/i";
        // $input_phone = preg_replace($special_chars, "", $input_phone);
        // $input_phone = str_split($input_phone, 3);
        // $input_phone = $input_phone[0] . "-" . $input_phone[1] . "-" . $input_phone[2] . $input_phone[3];
        $phone = $input_phone;
    }

    // Validate joinDate
    $input_joinDate = trim($_POST["joinDate"]);
    $date_array = explode("-", $input_joinDate);
    $curr_date = new DateTime();
    $joinDate_date = new DateTime($input_joinDate);

    if(empty($input_joinDate)){
        $joinDate_err = "Please enter a hire date.";     
    } else if (!checkdate(ltrim($date_array[1], "0"), ltrim($date_array[2], "0"), $date_array[0])) {
        $joinDate_err = "Please enter a valid date.";
    } else if ($joinDate_date > $curr_date){
        $joinDate_err = "Please enter a hire date that is in the present or past.";  
    }else{
        $joinDate = $input_joinDate;
    }

    // Validate birthday
    $input_birthday = trim($_POST["birthday"]);
    $date_array = explode("-", $input_birthday);
    $curr_date = new DateTime();
    $birthday_date = new DateTime($input_birthday);

    if(empty($input_birthday)){
        $birthday_err = "Please enter a hire date.";     
    } else if (!checkdate(ltrim($date_array[1], "0"), ltrim($date_array[2], "0"), $date_array[0])) {
        $birthday_err = "Please enter a valid date.";
    } else if ($birthday_date > $curr_date){
        $birthday_err = "Please enter a hire date that is in the present or past.";  
    }else{
        $birthday = $input_birthday;
    }
    
    // Check input errors before inserting in database
    if(empty($name_err) && empty($joinDate_err) && empty($address_err) && empty($phone_err) && empty($birthday_err)){
        // Prepare an insert statement
        $sql = "UPDATE Members SET name=?, address=?, phone=?, joinDate=?, birthday=? WHERE id=?"; 

        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssssi", $param_name, $param_address, $param_phone, $param_joinDate, $param_birthday, $param_id);
            
            // Set parameters
            $param_name = $name;
            $param_address = $address;
            $param_phone = $phone;
            $param_joinDate = $joinDate;
            $param_birthday = $birthday;
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to Members page
                header("location: index.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
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
      $sql = "SELECT * FROM Members WHERE id = ?";
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
                $name = $row["Name"];
                $address = $row["Address"];
                $phone = $row["Phone"];
                $joinDate = $row["JoinDate"];
                $birthday = $row["Birthday"];
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
    // mysqli_stmt_close($stmt);
    
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
  <title>Update Member</title>
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
            <a class="nav-link active" href="index.php">Members</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../transactions/index.php">Transactions</a>
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
              <h2 class="my-2">Update Member</h2>
            </div>
              <p>Please edit this form information and submit to update the selected member into the database.</p>
              <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                  <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                      <label>Name</label>
                      <input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
                      <span class="help-block text-danger"><?php echo $name_err;?></span>
                  </div>
                  <div class="form-group <?php echo (!empty($address_err)) ? 'has-error' : ''; ?>">
                      <label>Address</label>
                      <input type="text" name="address" class="form-control" value="<?php echo $address; ?>">
                      <span class="help-block text-danger"><?php echo $address_err;?></span>
                  </div>
                  <div class="form-group <?php echo (!empty($phone_err)) ? 'has-error' : ''; ?>">
                      <label>Phone</label>
                      <input type="text" name="phone" class="form-control" value="<?php echo $phone; ?>">
                      <span class="help-block text-danger"><?php echo $phone_err;?></span>
                  </div>
                  <div class="form-group <?php echo (!empty($joinDate_err)) ? 'has-error' : ''; ?>">
                      <label>Join Date</label>
                      <input type="date" name="joinDate" class="form-control" value="<?php echo $joinDate; ?>">
                      <span class="help-block text-danger"><?php echo $joinDate_err;?></span>
                  </div>
                  <div class="form-group <?php echo (!empty($birthday_err)) ? 'has-error' : ''; ?>">
                      <label>Birthday</label>
                      <input type="date" name="birthday" class="form-control" value="<?php echo $birthday; ?>">
                      <span class="help-block text-danger"><?php echo $birthday_err;?></span>
                  </div>
                  <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                  <input type="submit" class="mt-2 btn btn-primary" value="Submit">
                  <a href="index.php" class="mt-2 btn btn-default">Cancel</a>
              </form>
          </div>
      </div>        
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
</body>
</html>