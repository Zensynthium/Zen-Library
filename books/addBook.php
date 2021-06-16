<?php
// Include config file
require_once "../config.php";
 
// Define variables and initialize with empty values
$title = $ISBN = $author = $category = $price = "";
$title_err = $ISBN_err = $author_err = $category_err = $price_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate Title
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
    } else{
        $input_ISBN = filter_var($input_ISBN, FILTER_SANITIZE_STRING);
        $ISBN = $input_ISBN;
    }
    
    // Validate author
    $input_author = trim($_POST["author"]);

    if(empty($input_author)){
        $author_err = "Please enter an author.";     
    } else{
        $input_author = filter_var($input_author, FILTER_SANITIZE_STRING);
        $author = $input_author;
    }

    // Validate category
    $input_category = trim($_POST["category"]);
    if(empty($input_category)){
        $category_err = "Please enter a category.";     
    } else{
        $input_category = filter_var($input_category, FILTER_SANITIZE_STRING);
        $category = $input_category;
    }

    // Validate price
    $price_regex = '/[\d]{1,3}[.]{0,1}[\d]{0,2}/';
    $input_price = trim($_POST["price"]);
    if(empty($input_price)){
        $price_err = "Please enter the price amount.";     
    } elseif(intval($input_price) > 100){
        $price_err = "Please enter a price less than 100.";
    } elseif(!preg_match($price_regex, $input_price)){
        $price_err = "Please enter a number 1-100";
    }else{
        $price = $input_price;
    }
    
    // Check input errors before inserting in database
    if(empty($title_err) && empty($ISBN_err) && empty($author_err) && empty($category_err) && empty($price_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO Books (title, ISBN, author, category, availability, price) VALUES (?, ?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssid", $param_title, $param_ISBN, $param_author, $param_category, $param_availability, $param_price);
            
            // Set parameters
            $param_title = $title;
            $param_ISBN = $ISBN;
            $param_author = $author;
            $param_category = $category;
            $param_availability = 0;
            $param_price = $price;
            
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
  <title>Add Book</title>
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
            <a class="nav-link active" href="index.php">Books</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../employees/index.php">Employees</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../members/index.php">Members</a>
          </li>
          <li class="nav-item">
            <a class="nav-link " href="../transactions/index.php">Transactions</a>
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
              <h2 class="my-3">Add Book</h2>
            </div>
              <p>Please fill this form and submit to add books into the database.</p>
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
                  <div class="form-group <?php echo (!empty($price_err)) ? 'has-error' : ''; ?>">
                      <label>Price</label>
                      <input type="text" name="price" class="form-control" value="<?php echo $price; ?>">
                      <span class="help-block text-danger"><?php echo $price_err;?></span>
                  </div>
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