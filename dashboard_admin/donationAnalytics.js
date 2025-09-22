window.addEventListener('DOMContentLoaded', () => {
    // Donations by Cause Pie Chart
    if (document.getElementById('causePieChart')) {
        new Chart(document.getElementById('causePieChart'), {
            type: 'pie',
            data: {
                labels: causes,
                datasets: [{
                    data: causeTotals,
                    backgroundColor: ['#3a6fa0', '#2f5c89', '#b6cbdf', '#eaf6f6', '#d35400']
                }]
            }
        });
    }
    // Monthly Trend Bar Chart
    if (document.getElementById('monthBarChart')) {
        new Chart(document.getElementById('monthBarChart'), {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Rands',
                    data: monthTotals,
                    backgroundColor: '#3a6fa0'
                }]
            }
        });
    }
    // Top Donors Bar Chart
    if (document.getElementById('topDonorChart')) {
        new Chart(document.getElementById('topDonorChart'), {
            type: 'bar',
            data: {
                labels: topDonorNames,
                datasets: [{
                    label: 'Total Given (R)',
                    data: topDonorTotals,
                    backgroundColor: '#27ae60'
                }]
            }
        });
    }
});