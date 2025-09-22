<?php
require_once "../databaseConnection.php";


// Fetch number of procedures
$query = "
     SELECT medicalprocedure.procedureName, COUNT(*) AS total
    FROM medicalprocedure, medical_report 
    WHERE medical_report.procedureID = medicalprocedure.procedureID
    GROUP BY procedureName
    ORDER BY total DESC
";

$result = $conn->query($query);

$dataArray = [['Procedure', 'Total']];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dataArray[] = [$row['procedureName'], (int)$row['total']];
    }
}


$conn->close();
?>

<!---
create pie chart
----->
<div id="piechart" style="width: 600px; height: 400px;"></div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    var data = google.visualization.arrayToDataTable(<?php echo json_encode($dataArray); ?>);

    var options = {
        title: 'Number of Procedures by Type',
        legend: {position: 'bottom'}
    };

    var chart = new google.visualization.PieChart(document.getElementById('piechart'));
    chart.draw(data, options);
}
</script>