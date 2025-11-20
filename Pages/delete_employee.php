<?php
// Include your database connection file
include 'db_connect.php';

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect the employee ID from the POST request
    $employeeId = $_POST['EmployeeID'];

    // Sanitize the input
    $employeeId = $conn->real_escape_string($employeeId);

    // Prepare the SQL DELETE statement
    $sql = "DELETE FROM Employee WHERE EmployeeID = '$employeeId'";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        // If the deletion was successful
        echo "success";
    } else {
        // If there was an error
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the database connection
    $conn->close();
    header('Content-Type: application/json');
}

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error message']);
}
?>