<!DOCTYPE html>
<html lang="en">

<head>
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>All records results</title>
    <link rel="stylesheet" type="text/css" href="CrueltyReport.css">
</head>

<?php
    require 'Databaseconnection.php';

    $sql="SELECT * FROM cruelty_report";

    $result = $conn->query($sql);

    if($result ->num_rows >0){

        echo "<p><h2>All dog record(s) in the system </h2> </p>";
        echo "<center><table width =\"75%\" bgcolor = \"lightblue\"><tr bgcolor=\"orange\">
            <th>Cruelty Report ID</th> <th>StaffID</th><th>animal Details</th><th>Location</th><th>Investigation Status</th>
            <th>Rescue Circumstance</th><th>image</th><th>Update</th><th>Delete</th></tr>";

            while($row = $result->fetch_assoc()){ ?>
                <tr>
                    <td>
                        <?php echo $row["crueltyID"];?>
                    </td>

                    <td>
                        <?php echo $row["staffID"];?>
                    </td>
                    <td>
                        <?php echo $row["animalDetails"];?>
                    </td>
                    <td>
                        <?php echo $row["location"];?>
                    </td>
                    <td>
                        <?php echo $row["investigationStatus"];?>
                    </td>
                    <td>
                        <?php echo $row["rescueCircumstance"];?>
                    </td>
                    <td>
                        <img src="./images/<?php echo $row["picture"];?>">
                    </td>
                    <td>
                        <a href="ReportUpdaterecord.php?crueltyID=<?php echo $row["crueltyID"] ?>"><button>Update</button></a>
                    </td>
                    <td>
                        <a href="ReportDeleteRecord.php?crueltyID=<?php echo $row["crueltyID"] ?>"><button>Delete </button></a>
                    </td>

                </tr>

                <?php
            }

              
                echo "<p>No matching record found. Try using other search criteria </p>";
            }

            $conn->close();
            echo "</table></center>";
            echo"<p><a href= \"AddDogForm.html\"><button>Edit another report</button></a></p>";
            echo" <p><a href= \"displayAllrecords.php\"><button>Display All Cruelty Records </button></a></p>";
            echo"<p><a href=\"dogRecordSearchForm.html\"><button>Search for Cruelty record</button></a></p>";
            echo" <p> <a href=\"loginForm.html\"><button>Back to Login</button></a></p>";

?>
</body>
</html>
