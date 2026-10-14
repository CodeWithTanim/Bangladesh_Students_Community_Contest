<?php
/**
 * Bangladesh Students Community Contest Platform - Admin Dashboard Stats API
 */

header('Content-Type: application/json');

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';

// Enforce admin login check
require_login_api();

$db = DB::connect();

try {
    // 1. Total Submissions count
    $total_stmt = $db->query("SELECT COUNT(*) as total FROM submissions");
    $total_submissions = (int)$total_stmt->fetch()['total'];
    
    // 2. Department-wise Stats (for Bar Chart)
    $dept_stmt = $db->query("
        SELECT department, COUNT(*) as count 
        FROM submissions 
        GROUP BY department 
        ORDER BY count DESC
    ");
    $dept_stats = $dept_stmt->fetchAll();
    
    // 3. Semester-wise Stats (for Pie Chart)
    $sem_stmt = $db->query("
        SELECT semester, COUNT(*) as count 
        FROM submissions 
        GROUP BY semester 
        ORDER BY semester ASC
    ");
    $semester_stats = $sem_stmt->fetchAll();
    
    // 4. Submission Trends (Line Chart - Last 14 days)
    $trend_stmt = $db->query("
        SELECT DATE_FORMAT(created_at, '%b %d') as date_label, COUNT(*) as count 
        FROM submissions 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY) 
        GROUP BY DATE(created_at) 
        ORDER BY created_at ASC
    ");
    $trend_stats = $trend_stmt->fetchAll();
    
    // 5. Hour of Day Distribution (Histogram - 0 to 23 Hours)
    $hour_stmt = $db->query("
        SELECT HOUR(created_at) as hr, COUNT(*) as count 
        FROM submissions 
        GROUP BY HOUR(created_at) 
        ORDER BY hr ASC
    ");
    $hour_raw = $hour_stmt->fetchAll();
    
    // Pre-populate histogram array with 24 hours of 0s
    $hour_distribution = array_fill(0, 24, 0);
    foreach ($hour_raw as $row) {
        $hour_distribution[(int)$row['hr']] = (int)$row['count'];
    }
    
    // 6. Recent Submissions (Latest 5)
    $recent_stmt = $db->query("
        SELECT student_name, department, semester, created_at 
        FROM submissions 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $recent_submissions = $recent_stmt->fetchAll();
    
    // Format timestamps for recent submissions nicely
    foreach ($recent_submissions as &$sub) {
        $sub['time_ago'] = time_elapsed_string($sub['created_at']);
    }

    // Return payload
    echo json_encode([
        'success' => true,
        'data' => [
            'total_submissions' => $total_submissions,
            'department_stats' => $dept_stats,
            'semester_stats' => $semester_stats,
            'trend_stats' => $trend_stats,
            'hour_stats' => $hour_distribution,
            'recent_submissions' => $recent_submissions
        ]
    ]);

} catch (PDOException $e) {
    error_log("Failed to fetch admin statistics: " . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode([
        'success' => false,
        'message' => 'Database Error: Could not compile dashboard statistics.'
    ]);
}

/**
 * Helper function to calculate readable time-ago string
 */
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $w = floor($diff->d / 7);
    $d = $diff->d - ($w * 7);

    $parts = array(
        'y' => $diff->y,
        'm' => $diff->m,
        'w' => $w,
        'd' => $d,
        'h' => $diff->h,
        'i' => $diff->i,
        's' => $diff->s,
    );

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($parts[$k]) {
            $v = $parts[$k] . ' ' . $v . ($parts[$k] > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
