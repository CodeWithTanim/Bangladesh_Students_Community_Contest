<?php
/**
 * Bangladesh Students Community Contest Platform - Admin Login
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';

// If already logged in, redirect to dashboard
if (is_logged_in()) {
    header("Location: " . BASE_URL . "/admin/index.php");
    exit();
}

$error_message = '';
$username_val = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_val = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $csrf = $_POST['csrf_token'] ?? '';
    
    // 1. Verify CSRF Token
    if (!verify_csrf_token($csrf)) {
        $error_message = "Security Error: CSRF token verification failed.";
    } else {
        $db = DB::connect();
        $ip = get_client_ip();
        
        try {
            // 2. Bruteforce Protection - Check login failures in last 10 minutes
            $fail_stmt = $db->prepare("
                SELECT COUNT(*) as count 
                FROM logs 
                WHERE action = 'login_failure' 
                AND ip_address = :ip 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)
            ");
            $fail_stmt->execute(['ip' => $ip]);
            $fail_count = $fail_stmt->fetch()['count'];
            
            if ($fail_count >= 5) {
                $error_message = "Too many failed attempts. Access temporarily locked. Please try again in 10 minutes.";
            } else if (empty($username_val) || empty($password)) {
                $error_message = "Please enter both username and password.";
            } else {
                // 3. Query Admin details
                $stmt = $db->prepare("SELECT * FROM admins WHERE username = :username LIMIT 1");
                $stmt->execute(['username' => $username_val]);
                $admin = $stmt->fetch();
                
                if ($admin && password_verify($password, $admin['password'])) {
                    // Login Success
                    
                    // 4. Session Fixation Protection
                    session_regenerate_id(true);
                    
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['last_activity'] = time();
                    
                    // Log audit
                    log_action('login_success', "Admin '{$username_val}' logged in successfully.");
                    
                    // Redirect to targeted page or dashboard
                    $redirect_target = $_SESSION['login_redirect'] ?? (BASE_URL . '/admin/index.php');
                    unset($_SESSION['login_redirect']);
                    
                    header("Location: " . $redirect_target);
                    exit();
                } else {
                    // Login Fail
                    $error_message = "Invalid username or password.";
                    log_action('login_failure', "Failed login attempt for username: '{$username_val}'");
                }
            }
        } catch (PDOException $e) {
            error_log("Login processing query failure: " . $e->getMessage());
            $error_message = "A system error occurred. Please try again later.";
        }
    }
}

$page_title = "Admin Login";
require_once dirname(__DIR__) . '/includes/header.php';
?>

<section class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-6">
        
        <!-- Login Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex w-12 h-12 rounded-xl bg-emerald-500/10 items-center justify-center text-emerald-400 mb-2">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
            </div>
            <h1 class="font-outfit text-2xl sm:text-3xl font-extrabold text-white">Administrator Portal</h1>
            <p class="text-xs text-slate-400">Sign in to manage submissions and platform configuration</p>
        </div>

        <!-- Login Card -->
        <div class="glass-panel rounded-2xl p-6 sm:p-8 shadow-glass border-white/5 relative">
            
            <?php if (!empty($error_message)): ?>
                <!-- Alert Box -->
                <div class="mb-5 p-4 rounded-xl bg-rose-500/10 border border-rose-500/25 text-rose-300 text-xs flex items-start space-x-2">
                    <svg class="w-4 h-4 text-rose-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <span><?php echo esc($error_message); ?></span>
                </div>
            <?php endif; ?>

            <form action="<?php echo esc($_SERVER['REQUEST_URI']); ?>" method="POST" class="space-y-5">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                <!-- Username Input -->
                <div class="space-y-1.5">
                    <label for="username" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Username</label>
                    <div class="relative">
                        <input type="text" name="username" id="username" required value="<?php echo esc($username_val); ?>" placeholder="Enter admin username"
                               class="w-full pl-4 pr-10 py-3 rounded-xl bg-slate-950/40 border border-white/10 text-white text-sm focus:outline-none placeholder-slate-500 transition-all duration-200">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Password Input -->
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center">
                        <label for="password" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Password</label>
                    </div>
                    <div class="relative">
                        <input type="password" name="password" id="password" required placeholder="••••••••"
                               class="w-full pl-4 pr-10 py-3 rounded-xl bg-slate-950/40 border border-white/10 text-white text-sm focus:outline-none placeholder-slate-500 transition-all duration-200">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-bold text-sm tracking-wide transition-all duration-300 btn-glow-emerald">
                    Sign In
                </button>
            </form>
        </div>
        
        <!-- Back to Main site links -->
        <div class="text-center">
            <a href="<?php echo BASE_URL; ?>/index.php" class="text-xs text-slate-500 hover:text-emerald-400 transition-colors">
                &larr; Back to Student Submission Portal
            </a>
        </div>
    </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
