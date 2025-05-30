// This file contains the JavaScript code for the dashboard application.

function toggleDropdown(event, el) {
    event.preventDefault();
    document.querySelectorAll('.dropdown-content').forEach(dd => {
        if (!dd.previousElementSibling.isSameNode(el)) {
            dd.style.display = 'none';
        }
    });
    const dropdown = el.nextElementSibling;
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

// Optional: Hide dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.classList.contains('dropdown-toggle')) {
        document.querySelectorAll('.dropdown-content').forEach(dd => dd.style.display = 'none');
    }
});

window.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('myPieChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Miscellaneous Actions', 'General Government & Administration', 'Public Services'],
            datasets: [{
                label: 'My Dataset',
                data: [56, 19, 30],
                backgroundColor: ['#183B4E', '#27548A', '#DDA853']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const data = context.dataset.data;
                            const total = data.reduce((sum, val) => sum + val, 0);
                            const value = context.raw;
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${context.label}: ${percentage}%`;
                        }
                    }
                }
            }
        }
    });
});

// Add active class to clicked nav-link and remove from others
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function() {
        // Remove active from all nav-links
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        // Add active to the clicked link
        this.classList.add('active');
    });
});