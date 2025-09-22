// Color palette for consistency
const colors = {
    primary: '#ae9787',
    secondary: '#b6cbdf', 
    accent: '#ffd700',
    success: '#22c55e',
    warning: '#f59e0b',
    danger: '#ef4444',
    info: '#3b82f6',
    neutral: '#64748b',
    light: '#f8fafc'
};

// Common chart options
const commonOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                padding: 20,
                usePointStyle: true,
                font: {
                    size: 12,
                    family: "'Hind Siliguri', sans-serif"
                }
            }
        },
        tooltip: {
            backgroundColor: 'rgba(0,0,0,0.8)',
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            cornerRadius: 8,
            padding: 12
        }
    }
};

// 1. Status Pie Chart
const statusCtx = document.getElementById('statusPieChart').getContext('2d');
new Chart(statusCtx, {
    type: 'pie',
    data: {
        labels: statuses,
        datasets: [{
            data: counts,
            backgroundColor: [
                colors.primary,
                colors.secondary,
                colors.accent,
                colors.info,
                colors.success,
                colors.warning,
                colors.danger
            ],
            borderWidth: 2,
            borderColor: '#ffffff',
            hoverOffset: 8
        }]
    },
    options: {
        ...commonOptions,
        plugins: {
            ...commonOptions.plugins,
            legend: {
                ...commonOptions.plugins.legend,
                position: 'right'
            }
        }
    }
});

// 2. Monthly Trend Bar Chart
const monthCtx = document.getElementById('monthBarChart').getContext('2d');
new Chart(monthCtx, {
    type: 'bar',
    data: {
        labels: months,
        datasets: [{
            label: 'Reports',
            data: monthCounts,
            backgroundColor: colors.primary,
            borderColor: colors.primary,
            borderWidth: 1,
            borderRadius: 6,
            borderSkipped: false
        }]
    },
    options: {
        ...commonOptions,
        plugins: {
            ...commonOptions.plugins,
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Month',
                    font: { size: 14, weight: 'bold' }
                },
                grid: {
                    display: false
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Number of Reports',
                    font: { size: 14, weight: 'bold' }
                },
                beginAtZero: true,
                grid: {
                    color: 'rgba(174,151,135,0.1)'
                }
            }
        }
    }
});


// 5. Day of Week Radar Chart
if (days && days.length > 0) {
   // Reports by Day of Week - Line Chart
const dayCtx = document.getElementById('dayChart').getContext('2d');
new Chart(dayCtx, {
    type: 'line',
    data: {
        labels: days,   // PHP: ["Sunday","Monday",...]
        datasets: [{
            label: 'Reports',
            data: dayCounts, // PHP: [count1, count2,...]
            borderColor: '#3a6fa0',
            backgroundColor: 'rgba(58,111,160,0.2)',
            tension: 0.3,        // smooth curve
            fill: true,
            pointBackgroundColor: '#2c557a',
            pointBorderColor: '#fff',
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                mode: 'index',
                intersect: false
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Day of the Week'
                }
            },
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Number of Reports'
                },
                ticks: {
                    precision:0
                }
            }
        }
    }
});
}

// 6. Inspector Assignment Doughnut Chart
const inspectorCtx = document.getElementById('inspectorDoughnutChart').getContext('2d');
new Chart(inspectorCtx, {
    type: 'doughnut',
    data: {
        labels: ['Assigned', 'Unassigned'],
        datasets: [{
            data: [assignedCount, unassignedCount],
            backgroundColor: [colors.success, colors.warning],
            borderWidth: 3,
            borderColor: '#ffffff',
            hoverOffset: 6
        }]
    },
    options: {
        ...commonOptions,
        cutout: '65%',
        plugins: {
            ...commonOptions.plugins,
            legend: {
                ...commonOptions.plugins.legend,
                position: 'bottom'
            },
            tooltip: {
                ...commonOptions.plugins.tooltip,
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.raw / total) * 100).toFixed(1);
                        return `${context.label}: ${context.raw} (${percentage}%)`;
                    }
                }
            }
        }
    }
});

// 7. Location Chart (if location data exists)
if (locations && locations.length > 0) {
    const locationCtx = document.getElementById('locationChart').getContext('2d');
    new Chart(locationCtx, {
        type: 'bar',
        data: {
            labels: locations,
            datasets: [{
                label: 'Reports by Location',
                data: locationCounts,
                backgroundColor: colors.info,
                borderColor: colors.primary,
                borderWidth: 1,
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            ...commonOptions,
            plugins: {
                ...commonOptions.plugins,
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Location',
                        font: { size: 14, weight: 'bold' }
                    },
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Number of Reports',
                        font: { size: 14, weight: 'bold' }
                    },
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(174,151,135,0.1)'
                    }
                }
            }
        }
    });
}

// Add animation and interaction effects
document.querySelectorAll('.stats-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-8px) scale(1.02)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
    });
});

// Add loading animation for charts
document.querySelectorAll('canvas').forEach(canvas => {
    canvas.style.opacity = '0';
    canvas.style.transition = 'opacity 0.5s ease-in-out';
    
    setTimeout(() => {
        canvas.style.opacity = '1';
    }, 300);
});

// Print functionality
function printDashboard() {
    window.print();
}

// Export data functionality (if needed)
function exportToCSV() {
    const csvData = [
        ['Metric', 'Value'],
        ['Total Reports', totalReports || 0],
        ['Unassigned Reports', unassignedCount || 0],
        ['Recent Reports (30 days)', recentReports || 0],
        ['Overdue Cases', overdueReports || 0],
        ['Completion Rate', completionRate || 0 + '%'],
        ['Average Resolution Days', avgResolutionDays || 0]
    ];
    
    const csvContent = csvData.map(row => row.join(',')).join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'cruelty_report_analytics.csv';
    a.click();
}

// Auto-refresh data every 5 minutes (optional)
setInterval(() => {
    if (document.visibilityState === 'visible') {
        console.log('Auto-refreshing analytics data...');
        // You could add AJAX call here to refresh data
    }
}, 300000); // 5 minutes