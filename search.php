<?php
// Connection info, dummy info for XAMPP instance I was testing on, you guys...
// ...can update this next semester when we get access to the production db
$server = "localhost";
$username = "root";
//$password = "password";
$database = "test";

if (isset($_POST['search'])) {
    $name = $_POST['search'];

    // Initialize database connection, catch/print errors
    $connection = mysqli_connect($server,  $username, null, $database);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    // Send actual SQL query to server, store results in a set
    //**prepared statements here to prevent SQL injection, very important for security!**
    $query = "SELECT `Name`, `Category`, `Thumbnail Image` FROM `updated_hearatale` WHERE `Name` LIKE CONCAT('%', ?, '%') GROUP BY `Name`"; # Group by name to remove duplicates
    $stmt = $connection->prepare($query);
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $result = $stmt->get_result();

    $arr = [];
    // Loop through returned db results and print them to results page
    while ($row = $result->fetch_assoc()) {
        # Only three fields I care about rn, extract from SQL results
        $Name = $row['Name'];
        $Category = $row['Category'];
        $Thumbnail = $row['Thumbnail Image'];

        $temp_arr = array('thumb' => $Thumbnail, 'name' => $Name, 'category' => $Category);
        array_push($arr, $temp_arr);
    }
    # Encode our array of arrays to json for jquery
    echo json_encode($arr);
} else {
    echo  "<p>Please enter a search query</p>";
}
?>
