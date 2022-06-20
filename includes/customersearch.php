<?php
    //Our customer search/filtering engine
    include "./config.php"; //load in any variables
    $db_connection = mysqli_connect(DB_HOSTNAME, DB_USER, DB_PASSWORD, DB_DATABASE) or die();

    //do some simple validation to check if sq contains a string
    $sq = $_GET['sq'];
    $searchresult = '';

    if (isset($sq) and !empty($sq) and strlen($sq) < 31) {
        $sq = strtolower($sq);

        //prepare a query and send it to the server using our search string as a wildcard on surname
        $query = "SELECT customerID,firstname,lastname FROM customer WHERE lastname LIKE '$sq%' ORDER BY lastname";
        $result = mysqli_query($db_connection, $query);
        $rowcount = mysqli_num_rows($result); 
        
        //makes sure we have customers
        if ($rowcount > 0) {  

            $rows=[]; //start an empty array
            
            //append each row in the query result to our empty array until there are no more results                    
            while ($row = mysqli_fetch_assoc($result)) {            
                $rows[] = $row; 
            }

            //take the array of our 1 or more customers and turn it into a JSON text
            $searchresult = json_encode($rows);

            //this line is cruicial for the browser to understand what data is being sent
            header('Content-Type: text/json; charset=utf-8');

        } 
        else {
            echo "<tr><td colspan=3> <h2>Invalid search query</h2>";
        }
    } 
    else {
        echo "<tr><td colspan=3> <h2>Invalid search query</h2>";
    }

    mysqli_free_result($result); //free any memory used by the query
    mysqli_close($db_connection); //close the connection once done

    echo  $searchresult;