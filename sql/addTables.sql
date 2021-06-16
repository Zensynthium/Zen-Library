CREATE TABLE Books(
  ID int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  Title varchar(255),
  ISBN varchar(255),
  Author varchar(255),
  Category varchar(255),
  Availability bool,
  Price decimal(4,2) UNSIGNED
);

CREATE TABLE Members(
  ID int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  Name varchar(255),
  Address varchar(255),
  Phone varchar(255),
  JoinDate date,
  Birthday date
);

CREATE TABLE Employees(
  ID int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  Name varchar(255),
  Position varchar(255),
  Address varchar(255),
  Phone varchar(255),
  HireDate date
);

CREATE TABLE Transactions(
  TransID int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  BookID int UNSIGNED,
  MemberID int UNSIGNED,
  EmployeeID int UNSIGNED,
  TransDate date,
  IsCheckOut bool,
  -- isBuy bool
  CONSTRAINT `fk_book_id`
    FOREIGN KEY (BookID) REFERENCES Books (ID)
    ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `fk_member_id`  
    FOREIGN KEY (MemberID) REFERENCES Members (ID)
    ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `fk_employee_id`
    FOREIGN KEY (EmployeeID) REFERENCES Employees (ID)
    ON DELETE CASCADE ON UPDATE RESTRICT
);
