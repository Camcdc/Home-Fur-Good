// Chart configuration and initialization
document.addEventListener('DOMContentLoaded', function() {
    // Color schemes for charts
    const primaryColors = [
        '#2c5aa0', '#1a3d73', '#17a2b8', '#28a745', '#ffc107', 
        '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c'
    ];
    
    const statusColors = {
        'Pending': '#ffc107',
        'Approved': '#28a745',
        'Rejected': '#dc3545',
        'Under Review': '#17a2b8'
    };

    // 1. Application Status Pie Chart
    if (document.getElementById('statusPieChart')) {
        const statusCtx = document.getElementById('statusPieChart').getContext('2d');
        const statusChartColors = statuses.map(status => statusColors[status] || primaryColors[0]);
        
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: statuses,
                datasets: [{
                    data: statusCounts,
                    backgroundColor: statusChartColors,
                    borderColor: statusChartColors.map(color => color + '80'),
                    borderWidth: 2,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.raw / total) * 100).toFixed(1);
                                return `${context.label}: ${context.raw} (${percentage}%)`;
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    duration: 1500
                }
            }
        });
    }

    // 2. Monthly Applications Bar Chart
    if (document.getElementById('monthBarChart')) {
        const monthCtx = document.getElementById('monthBarChart').getContext('2d');
        
        new Chart(monthCtx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Applications',
                    data: monthCounts,
                    backgroundColor: primaryColors[0] + '80',
                    borderColor: primaryColors[0],
                    borderWidth: 2,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                return context[0].label;
                            },
                            label: function(context) {
                                return `Applications: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        grid: {
                            color: '#e0e0e0'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                animation: {
                    delay: (context) => context.dataIndex * 100,
                    duration: 1000
                }
            }
        });
    }

    // 3. Task Type Distribution Chart
    if (document.getElementById('taskTypeChart')) {
        const taskTypeCtx = document.getElementById('taskTypeChart').getContext('2d');
        
        new Chart(taskTypeCtx, {
            type: 'doughnut',
            data: {
                labels: taskTypes,
                datasets: [{
                    data: taskTypeCounts,
                    backgroundColor: primaryColors,
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.raw / total) * 100).toFixed(1);
                                return `${context.label}: ${context.raw} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '60%',
                animation: {
                    animateRotate: true,
                    duration: 1500
                }
            }
        });
    }

    // 4. Task Completion Status Chart
    if (document.getElementById('taskCompletionChart')) {
        const taskCompletionCtx = document.getElementById('taskCompletionChart').getContext('2d');
        
        new Chart(taskCompletionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Active'],
                datasets: [{
                    data: [completedTasks, activeTasks],
                    backgroundColor: ['#28a745', '#ffc107'],
                    borderColor: '#fff',
                    borderWidth: 3,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = completedTasks + activeTasks;
                                const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                                return `${context.label}: ${context.raw} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '50%',
                animation: {
                    animateRotate: true,
                    duration: 1200
                }
            }
        });
    }

    // 5. Top Volunteers Chart
    if (document.getElementById('topVolunteersChart') && topVolunteerNames.length > 0) {
        const topVolunteersCtx = document.getElementById('topVolunteersChart').getContext('2d');
        
        new Chart(topVolunteersCtx, {
            type: 'bar',
            data: {
                labels: topVolunteerNames.slice(0, 10), // Show top 10
                datasets: [{
                    label: 'Completed Tasks',
                    data: topVolunteerTasks.slice(0, 10),
                    backgroundColor: primaryColors[3] + '80',
                    borderColor: primaryColors[3],
                    borderWidth: 2,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // Horizontal bar chart
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                return context[0].label;
                            },
                            label: function(context) {
                                return `Completed Tasks: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        grid: {
                            color: '#e0e0e0'
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                animation: {
                    delay: (context) => context.dataIndex * 150,
                    duration: 1200
                }
            }
        });
    }

    // 6. Application Activity by Day Chart
    if (document.getElementById('dayChart') && days.length > 0) {
        const dayCtx = document.getElementById('dayChart').getContext('2d');
        
        new Chart(dayCtx, {
            type: 'line',
            data: {
                labels: days,
                datasets: [{
                    label: 'Applications',
                    data: dayCounts,
                    borderColor: primaryColors[2],
                    backgroundColor: primaryColors[2] + '20',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: primaryColors[2],
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            title: function(context) {
                                return context[0].label;
                            },
                            label: function(context) {
                                return `Applications: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        grid: {
                            color: '#e0e0e0'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }

    // Add loading states and error handling
    const charts = document.querySelectorAll('canvas');
    charts.forEach(canvas => {
        const ctx = canvas.getContext('2d');
        if (!ctx) {
            console.warn(`Failed to get context for canvas: ${canvas.id}`);
            // Add fallback content
            const parent = canvas.parentElement;
            const fallback = document.createElement('div');
            fallback.className = 'chart-fallback';
            fallback.innerHTML = '<p>Chart unavailable</p>';
            parent.appendChild(fallback);
        }
    });

    // Add chart resize handling
    window.addEventListener('resize', function() {
        Chart.instances.forEach(chart => {
            chart.resize();
        });
    });

    // Add export functionality for charts
    function exportChart(chartId, filename) {
        const canvas = document.getElementById(chartId);
        if (canvas) {
            const url = canvas.toDataURL('image/png');
            const a = document.createElement('a');
            a.href = url;
            a.download = filename || 'chart.png';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    }

    // Make export function globally available
    window.exportChart = exportChart;

    // Add print-friendly styles
    const printStyles = `
        @media print {
            .chart-card {
                page-break-inside: avoid;
                margin-bottom: 20px;
            }
            .stats-cards {
                display: grid !important;
                grid-template-columns: repeat(3, 1fr) !important;
            }
        }
    `;
    
    const style = document.createElement('style');
    style.textContent = printStyles;
    document.head.appendChild(style);

    console.log('Volunteer Analytics charts initialized successfully');
});