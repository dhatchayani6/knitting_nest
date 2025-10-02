<?php
include "../includes/config.php"; // DB connection
?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="css/vendor.css">
    <link rel="stylesheet" id="theme-style" href="css/app.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body>
    <div class="main-wrapper">
        <div class="app" id="app">
            <?php include('includes/header.php') ?>
            <?php include('includes/sidebar.php') ?>
            <div class="sidebar-overlay" id="sidebar-overlay"></div>
            <div class="sidebar-mobile-menu-handle" id="sidebar-mobile-menu-handle"></div>
            <div class="mobile-menu-handle"></div>

            <article class="content dashboard-page bg-white">
                <section class="section card shadow border p-3">
                    <div class="container mt-4">
                        <h6>Stock Notifications</h6>

                        <!-- Alert Container -->
                        <div id="notificationContainer"></div>

                        <!-- Audio Alarm -->
                        <audio id="alarmSound" loop>
                            <source src="../sounds/alarmsound.wav" type="audio/wav">
                            Your browser does not support the audio element.
                        </audio>

                        <script>
const alarm = document.getElementById('alarmSound');
let alarmPlaying = false;

// Function to fetch notifications and play alarm if needed
function fetchNotifications() {
    const container = $('#notificationContainer');
    if (!container.length) return; // Ensure container exists

    $.ajax({
        url: 'api/fetch_low_stock.php', // Your updated API
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            container.html('');

            if (data.success && data.count > 0) {
                let html = '<div class="alert alert-warning mt-3"><strong>Low Stock Alert!</strong><ul>';
                data.notifications.forEach(item => {
                    html += `<li>${item.message} (Shop: ${item.shop_name})</li>`;
                });
                html += '</ul></div>';

                container.html(html);

                // Play alarm if not already playing
                if (!alarmPlaying) {
                    alarm.play().catch(err => console.log('Autoplay blocked:', err));
                    alarmPlaying = true;
                }
            } else {
                container.html('<div class="alert alert-success mt-3">No notifications found.</div>');

                // Stop alarm if it was playing
                if (alarmPlaying) {
                    alarm.pause();
                    alarm.currentTime = 0;
                    alarmPlaying = false;
                }
            }
        },
        error: function (err) {
            console.error('Error fetching notifications:', err);
        }
    });
}

// Fetch immediately on page load
fetchNotifications();

// Repeat every 1 minute
setInterval(fetchNotifications, 60000);

// Optional: Stop alarm if navigating away (clicking sidebar links)
document.querySelectorAll('.sidebar a, .sidebar li').forEach(link => {
    link.addEventListener('click', () => {
        if (alarmPlaying) {
            alarm.pause();
            alarm.currentTime = 0;
            alarmPlaying = false;
        }
    });
});
</script>




                    </div>
                </section>
            </article>
        </div>
    </div>
    <script src="js/vendor.js"></script>
    <script src="js/app.js"></script>
</body>

</html>