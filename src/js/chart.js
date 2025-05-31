// This script creates a pie chart using Chart.js
const ctx = document.getElementById('myPieChart').getContext('2d');

// Detect screen size
const isMobile = window.innerWidth <= 768;

// Define data
const data = {
    labels: ['Education', 'Infrastructure', 'Public Safety', 'Health and Sanitation', 'Administrative Costs', 'Environmental Services', 'Others / Miscellaneous'],
    datasets: [{
        data: [30, 20, 15, 10, 12, 8, 5],
        backgroundColor: ['#4e73df', '#858796', '#36b9cc', '#f6c23e', '#e74a3b', '#1cc88a', '#fd7e14'],
        borderWidth: 1
    }]
};

// Define chart options based on device type
const config = {
    type: 'pie',
    data: data,
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    font: {
                        size: isMobile ? 14 : 22
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        let value = context.raw || 0;
                        return `${label}: ${value}%`;
                    }
                }
            }
        }
    }
};

new Chart(ctx, config);

// This script creates a bar chart using Chart.js
const ctx2 = document.getElementById('budgetLineChart').getContext('2d');

new Chart(ctx2, {
  type: 'line',
  data: {
    labels: ['2022', '2023', '2024', '2025'],
    datasets: [{
      label: 'Municipal Budget',
      data: [80000000, 90000000, 100000000, 110000000], // in PHP
      borderColor: '#4e73df',
      backgroundColor: 'rgba(78, 115, 223, 0.1)',
      fill: true,
      tension: 0.3,
      pointBackgroundColor: '#4e73df',
      pointRadius: 5,
      pointHoverRadius: 7
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      title: {
        display: true,
        text: 'Municipal Budget (₱)',
        font: {
          size: 18
        }
      },
      tooltip: {
        callbacks: {
          label: function(context) {
            const value = context.raw;
            return '₱' + value.toLocaleString();
          }
        }
      },
      legend: {
        labels: {
          font: {
            size: 14
          }
        }
      }
    },
    scales: {
      y: {
        title: {
          display: true,
          text: 'Budget (₱)',
          font: {
            size: 14
          }
        },
        ticks: {
          callback: function(value) {
            return '₱' + value.toLocaleString();
          }
        },
        beginAtZero: true
      },
      x: {
        title: {
          display: true,
          text: 'Year',
          font: {
            size: 14
          }
        }
      }
    }
  }
});
