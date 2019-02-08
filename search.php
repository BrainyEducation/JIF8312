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

    // Create a list of all the queries we will run during the lookup - include original search string as first element
    $splitString = explode(' ', $name);
    $splitString = array_filter($splitString, "filterWords"); // remove articles from search
    $paramsType = str_repeat("s", sizeof($splitString) + 1); // the type for each text item (used for bind_param)
    array_unshift($splitString, $paramsType, $name);

    $queryList = []; // what we will include in the SQL statement
    $paramsList = []; // array we will use to bind the text to the SQL statement
    foreach($splitString as $key => &$item) {
        array_push($queryList, "`Name` LIKE CONCAT('%', ?, '%')");
        $paramsList[$key] = &$item; // hack for calling call_user_func_array() using references
    }
    array_shift($queryList); // removes the 'name like sss... case' from SQL statement

    // Send actual SQL query to server, store results in a set
    //**prepared statements here to prevent SQL injection, very important for security!**
    $query = "SELECT `Name`, `Category`, `Thumbnail Image` FROM `updated_hearatale` WHERE " . implode(" OR ", $queryList) . " GROUP BY `Name`"; # Group by name to remove duplicates
    $stmt = $connection->prepare($query);
    call_user_func_array(array($stmt, 'bind_param'), $paramsList);
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

// callback that filters out words we don't want to search
function filterWords($word) {
    $blacklist = array(
                    'The', 'the', 
                    'At', 'at', 
                    'And', 'and',
                    'Or', 'or',
                    'In', 'in',
                    'A', 'a',
                    'Of', 'of',
                    'To', 'to'
                );
    return !in_array($word, $blacklist);
}
?>
