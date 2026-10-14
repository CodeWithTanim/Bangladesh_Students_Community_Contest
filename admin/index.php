<?php
/**
 * Bangladesh Students Community Contest Platform - Admin Dashboard
 */

require_once dirname(__DIR__) . '/includes/auth.php';

// Enforce admin login
require_login();

$page_title = "Admin Dashboard";
require_once dirname(__DIR__) . '/includes/header.php';
?>

<!-- Include Chart.js via CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div id="admin-dashboard-container" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    
    <!-- Admin Control Sub-Navigation Bar -->
    <div class="glass-panel rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-md">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                </svg>
            </div>
            <div>
                <h1 class="font-outfit text-xl font-bold text-white leading-tight">Admin Control Panel</h1>
                <p class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Welcome back, <?php echo esc($_SESSION['admin_username']); ?></p>
            </div>
        </div>
        
        <!-- Controls -->
        <div class="flex flex-wrap items-center justify-center gap-2">
            <a href="index.php" class="px-4 py-2 rounded-lg text-xs font-semibold bg-emerald-500 text-darkbg hover:bg-emerald-400 transition-colors shadow-sm">
                Overview
            </a>
            <a href="submissions.php" class="px-4 py-2 rounded-lg text-xs font-semibold bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white transition-colors">
                Submissions
            </a>
            <a href="settings.php" class="px-4 py-2 rounded-lg text-xs font-semibold bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white transition-colors">
                Settings
            </a>
            <span class="w-[1px] h-5 bg-white/10 mx-1"></span>
            <a href="logout.php" class="px-4 py-2 rounded-lg text-xs font-semibold bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 hover:border-rose-500/30 transition-all duration-200">
                Sign Out
            </a>
        </div>
    </div>

    <!-- Status Cards & Counters -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <!-- Counter 1: Total Entries -->
        <div class="glass-panel rounded-2xl p-6 flex items-center justify-between shadow-sm relative overflow-hidden group">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Total Entries</span>
                <span id="total-submissions-counter" class="font-outfit text-4xl font-extrabold text-white transition-all duration-300 inline-block">0</span>
                <span class="text-[10px] text-slate-500 block">Submitted essays in system</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
            </div>
        </div>

        <!-- System Status Info -->
        <div class="glass-panel rounded-2xl p-6 flex items-center justify-between shadow-sm relative overflow-hidden group">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Platform State</span>
                <span class="font-outfit text-2xl font-bold text-white flex items-center">
                    <?php echo (int)get_setting('is_contest_active', '1') ? 'Submissions Open' : 'Submissions Closed'; ?>
                </span>
                <span class="text-[10px] text-slate-500 block">Configured via settings panel</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
            </div>
        </div>

        <!-- Sync Monitor -->
        <div class="glass-panel rounded-2xl p-6 flex items-center justify-between shadow-sm relative overflow-hidden group">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Syncing Status</span>
                <span class="font-outfit text-2xl font-bold text-white flex items-center space-x-2">
                    <span class="relative flex h-3 w-3">
                        <span id="sync-indicator" class="absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <span id="sync-status-text" class="text-sm font-semibold tracking-wide">Connecting...</span>
                </span>
                <span class="text-[10px] text-slate-500 block">Auto-polling active (5s intervals)</span>
            </div>
            <button type="button" onclick="fetchStats()" class="w-12 h-12 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 flex items-center justify-center flex-shrink-0 transition-colors focus:outline-none" title="Force Refresh">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Charts Grid (2x2 Structure) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Trend (Line Chart) -->
        <div class="glass-panel rounded-2xl p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-white/5 pb-3">
                <h3 class="font-outfit font-bold text-white text-sm tracking-wide">Submission Trends (Last 14 Days)</h3>
                <span class="text-[9px] font-bold bg-white/5 text-slate-400 px-2 py-0.5 rounded-full">Line Chart</span>
            </div>
            <div class="h-64 relative">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Department statistics (Bar Chart) -->
        <div class="glass-panel rounded-2xl p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-white/5 pb-3">
                <h3 class="font-outfit font-bold text-white text-sm tracking-wide">Entries by Department</h3>
                <span class="text-[9px] font-bold bg-white/5 text-slate-400 px-2 py-0.5 rounded-full">Bar Chart</span>
            </div>
            <div class="h-64 relative">
                <canvas id="deptChart"></canvas>
            </div>
        </div>

        <!-- Semester distribution (Pie Chart) -->
        <div class="glass-panel rounded-2xl p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-white/5 pb-3">
                <h3 class="font-outfit font-bold text-white text-sm tracking-wide">Semester-wise Statistics</h3>
                <span class="text-[9px] font-bold bg-white/5 text-slate-400 px-2 py-0.5 rounded-full">Pie Chart</span>
            </div>
            <div class="h-64 relative">
                <canvas id="semesterChart"></canvas>
            </div>
        </div>

        <!-- Hourly Frequency (Histogram) -->
        <div class="glass-panel rounded-2xl p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-white/5 pb-3">
                <h3 class="font-outfit font-bold text-white text-sm tracking-wide">Submission Hour Distribution</h3>
                <span class="text-[9px] font-bold bg-white/5 text-slate-400 px-2 py-0.5 rounded-full">Histogram</span>
            </div>
            <div class="h-64 relative">
                <canvas id="hourChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Submissions List -->
    <div class="glass-panel rounded-2xl p-5 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-white/5 pb-3">
            <div class="flex items-center space-x-2">
                <h3 class="font-outfit font-bold text-white text-sm tracking-wide">Recent Submissions</h3>
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
            </div>
            <a href="submissions.php" class="text-xs text-emerald-400 hover:text-emerald-300 font-semibold transition-colors">
                View All &rarr;
            </a>
        </div>
        <div id="recent-uploads-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Dynamic stats elements loaded by admin.js -->
            <div class="col-span-full py-12 flex justify-center items-center">
                <div class="loader-spinner"></div>
            </div>
        </div>
    </div>
</div>

<!-- Add the dashboard script before footer loads -->
<script src="<?php echo BASE_URL; ?>/assets/js/admin.js"></script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
