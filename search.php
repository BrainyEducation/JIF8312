<?php
// Connection info, dummy info for XAMPP instance I was testing on, you guys...
// ...can update this next semester when we get access to the production db
$server = "localhost";
$username = "root";
//$password = "password";
$database = "test";

if (isset($_POST['search'])) {
    $name = $_POST['search'];

    $language = 'English';
    if(isset($_POST['languageFilter'])) {
        $language = $_POST['languageFilter'];
    }

    //Error with accented names
    //COLLATE Latin1_General_CI_AI

    // Initialize database connection, catch/print errors
    $connection = mysqli_connect($server,  $username, null, $database);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $languageQuery = " `Category` != 'Spanish' AND `Category` != 'World Languages' "; #only include English results
    // search by language
    if($language=='Spanish') {
        $languageQuery= "Category='Spanish'";
    } elseif ($language != 'English') {
        $languageQuery = "Category='World Languages' AND Subcategory='$language'";
    }

    // search by section
    $sectionQuery = '';
    if (!empty($_POST['section1']) && !empty($_POST['section2'])) { $sectionQuery = $sectionQuery . "AND (`Section`='Children' OR `Section`='Students and Adults') "; }
        elseif (!empty($_POST['section1'])) { $sectionQuery = $sectionQuery . "AND `Section`='Children' ";}
        elseif (!empty($_POST['section2'])) {$sectionQuery = $sectionQuery . "AND `Section`='Students and Adults' ";}

    // search by original string + individual words in string
    $splitString = explode(' ', $name);
    $splitString = array_filter($splitString, "filterWords"); // remove articles from search
    array_unshift($splitString, $name);
    $paramsType = str_repeat("s", sizeof($splitString)); // the type for each text item (used for bind_param)
    array_unshift($splitString, $paramsType);

    $queryList = []; // what we will include in the SQL statement
    $paramsList = []; // array we will use to bind the text to the SQL statement
    foreach($splitString as $key => &$item) {
        array_push($queryList, "`Name` LIKE CONCAT('%', ?, '%')");
        $paramsList[$key] = &$item; // hack for calling call_user_func_array() using references
    }
    array_shift($queryList); // removes the 'name like sss... case' from SQL statement
    $stringQuery = "AND (" . implode(" OR ", $queryList) . ") ";

    // Send actual SQL query to server, store results in a set
    //**prepared statements here to prevent SQL injection, very important for security!**
    $query = "SELECT `Name`, `Category`, `Subcategory`, `Thumbnail Image`, `Home Experiences`
        FROM `updated_hearatale`
        WHERE
        $languageQuery
        $sectionQuery
        $stringQuery
        GROUP BY `Name`"; # Group by name to remove duplicates
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
        $Subcategory = $row['Subcategory'];
        $Thumbnail = $row['Thumbnail Image'];
        $Experiences = $row['Home Experiences'];

        $temp_arr = array('thumb' => $Thumbnail, 'name' => $Name, 'category' => $Category, 'subcategory' => $Subcategory, 'experiences' => $Experiences);
        array_push($arr, $temp_arr);
    }
    //Reverse the array to show the name first to show by relevance
    $arr = array_reverse($arr);
    try {
        if($arr != []) {
            # Encode our array of arrays to json for jquery
            echo json_encode($arr);
        } else {
            throw new Exception('No Results', 123);
        }
    } catch (Exception $e) {
        echo json_encode(array(
                        'error' => array(
                                    'msg' => $e->getMessage(),
                                    'code' => $e->getCode(),
                        ),
        ));
    }
} else {
    echo  "<p>Please enter a search query</p>";
    //echo json_encode(['error : true']);
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