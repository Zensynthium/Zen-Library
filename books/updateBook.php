<?php
// Error catching 

// ini_set('display_errors', 1); 
// ini_set('display_startup_errors', 1); 
// error_reporting(E_ALL);

// Include config file
require_once "../config.php";
 
// Define variables and initialize with empty values
$title = $ISBN = $author = $category = $availability = $price = "";
$title_err = $ISBN_err = $author_err = $category_err = $availability_err = $price_err = "";
 

// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
  // Get hidden input value
  $id = $_POST["id"];

    // Validate title
    $input_title = trim($_POST["title"]);
    if(empty($input_title)){
      $title_err = "Please enter a title.";
    } else {
      $input_title = filter_var($input_title, FILTER_SANITIZE_STRING);
      $title = $input_title;
    }
    
    // Validate ISBN
    $input_ISBN = trim($_POST["ISBN"]);
    if(empty($input_ISBN)){
        $ISBN_err = "Please enter an ISBN.";     
    } else {
        $input_ISBN = filter_var($input_ISBN, FILTER_SANITIZE_STRING);
        $ISBN = $input_ISBN;
    }
    
    // Validate author
    $input_author = trim($_POST["author"]);
    if(empty($input_author)){
        $author_err = "Please enter an author.";     
    } else {
        $input_author = filter_var($input_author, FILTER_SANITIZE_STRING);
        $author = $input_author;
    }

    // Validate category
    $input_category = trim($_POST["category"]);
    if(empty($input_category)){
      $category_err = "Please enter a category.";     
    } else {
      $input_category = filter_var($input_category, FILTER_SANITIZE_STRING);
      $category = $input_category;
    }

    // Validate availability
    $input_availability = trim($_POST["availability"]);
    if(empty($input_availability)){
      $availability_err = "Please enter the availability.";     
    } else {
      // $input_availability = filter_var($input_availability, FILTER_SANITIZE_STRING);
      $availability = $input_availability;
    }

    // Validate price
    $price_regex = '/[\d]{1,3}[.]{0,1}[\d]{0,2}/';
    $input_price = trim($_POST["price"]);
    if(empty($input_price)){
        $price_err = "Please enter the price amount.";     
    } elseif(intval($input_price) < 0 || intval($input_price) > 100){
        $price_err = "Please enter a price greater than 0 and less than 100.";
    } elseif(!preg_match($price_regex, $input_price)){
        $price_err = "Please enter a number 1-100 (ex: 23 or 14.99)";
    }else{
        $price = $input_price;
    }
    
    // Check input errors before inserting in database
    if(empty($title_err) && empty($ISBN_err) && empty($author_err) && empty($category_err) && empty($price_err)){
        // Prepare an insert statement
        $sql = "UPDATE Books SET title=?, ISBN=?, author=?, category=?, availability=?, price=? WHERE id=?"; 

        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssidi", $param_title, $param_ISBN, $param_author, $param_category, $param_availability, $param_price, $param_id);
            
            // Set parameters
            $param_title = $title;
            $param_ISBN = $ISBN;
            $param_author = $author;
            $param_category = $category;
            $param_availability = $availability;
            $param_price = $price;
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to Books page
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
      $sql = "SELECT * FROM Books WHERE id = ?";
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
                $title = $row["Title"];
                $ISBN = $row["ISBN"];
                $author = $row["Author"];
                $category = $row["Category"];
                $availability = $row["Availability"];
                $price = $row["Price"];
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
  <title>Update Book</title>
</head>
<body>
  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark pb-2 text-center">
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
            <a class="nav-link active" href="index.php">Books</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../employees/index.php">Employees</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../members/index.php">Members</a>
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
              <h2 class="my-3">Update Book</h2>
            </div>
              <p>Please edit this form information and submit to update the selected book into the database.</p>
              <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                  <div class="form-group <?php echo (!empty($title_err)) ? 'has-error' : ''; ?>">
                      <label>Title</label>
                      <input type="text" name="title" class="form-control" value="<?php echo $title; ?>">
                      <span class="help-block text-danger"><?php echo $title_err;?></span>
                  </div>
                  <div class="form-group <?php echo (!empty($ISBN_err)) ? 'has-error' : ''; ?>">
                      <label>ISBN</label>
                      <input type="text" name="ISBN" class="form-control" value="<?php echo $ISBN; ?>">
                      <span class="help-block text-danger"><?php echo $ISBN_err;?></span>
                  </div>
                  <div class="form-group <?php echo (!empty($author_err)) ? 'has-error' : ''; ?>">
                      <label>Author</label>
                      <input type="text" name="author" class="form-control" value="<?php echo $author; ?>">
                      <span class="help-block text-danger"><?php echo $author_err;?></span>
                  </div>
                  <div class="form-group <?php echo (!empty($category_err)) ? 'has-error' : ''; ?>">
                      <label>Category</label>
                      <input type="text" name="category" class="form-control" value="<?php echo $category; ?>">
                      <span class="help-block text-danger"><?php echo $category_err;?></span>
                  </div>
                  <div class="form-group <?php echo (!empty($availability_err)) ? 'has-error' : ''; ?>">
                      <label>Availability</label>
                      <select name="availability" class="form-control">
                        <!-- <option value="" disabled hidden></option> -->
                        <option <?php ($availability == '0' ? 'selected' : ''); ?> value="0">Available</option>
                        <option <?php ($availability == '1' ? 'selected' : ''); ?> value="1">Not Available</option>
                      </select>
                      <span class="help-block text-danger"><?php echo $availability_err;?></span>
                  </div>
                  <div class="form-group <?php echo (!empty($price_err)) ? 'has-error' : ''; ?>">
                      <label>Price</label>
                      <input type="text" name="price" class="form-control" value="<?php echo $price; ?>">
                      <span class="help-block text-danger"><?php echo $price_err;?></span>
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