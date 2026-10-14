<?php
/**
 * Bangladesh Students Community Contest Platform - Submission Management
 */

require_once dirname(__DIR__) . '/includes/auth.php';

// Enforce admin login
require_login();

$page_title = "Manage Submissions";
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div id="submissions-table-container" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    
    <!-- Admin Control Sub-Navigation Bar -->
    <div class="glass-panel rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-md">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
            </div>
            <div>
                <h1 class="font-outfit text-xl font-bold text-white leading-tight">Manage Submissions</h1>
                <p class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Verify and manage student entries</p>
            </div>
        </div>
        
        <!-- Controls -->
        <div class="flex flex-wrap items-center justify-center gap-2">
            <a href="index.php" class="px-4 py-2 rounded-lg text-xs font-semibold bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white transition-colors">
                Overview
            </a>
            <a href="submissions.php" class="px-4 py-2 rounded-lg text-xs font-semibold bg-emerald-500 text-darkbg hover:bg-emerald-400 transition-colors shadow-sm">
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

    <!-- Search / Filter Area -->
    <div class="glass-panel rounded-2xl p-6 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Keyword Search -->
        <div class="md:col-span-2 space-y-1">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Search Student / Dept</label>
            <div class="relative">
                <input type="text" id="search-input" placeholder="Search by name or department..." 
                       class="w-full pl-4 pr-10 py-2.5 rounded-xl bg-slate-950/40 border border-white/10 text-white text-xs placeholder-slate-500 focus:outline-none transition-all duration-200">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-500">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.602 10.602z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filter by Department (Dynamically populated by JS) -->
        <div class="space-y-1">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Filter Department</label>
            <select id="filter-dept" class="w-full px-3 py-2.5 rounded-xl bg-slate-950/40 border border-white/10 text-white text-xs focus:outline-none cursor-pointer">
                <option value="">All Departments</option>
            </select>
        </div>

        <!-- Filter by Semester -->
        <div class="space-y-1">
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Filter Semester</label>
            <select id="filter-semester" class="w-full px-3 py-2.5 rounded-xl bg-slate-950/40 border border-white/10 text-white text-xs focus:outline-none cursor-pointer">
                <option value="">All Semesters</option>
                <?php for ($i = 1; $i <= 8; $i++): ?>
                    <option value="Semester <?php echo $i; ?>">Semester <?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
        </div>
    </div>

    <!-- Live Status Update Info -->
    <div class="flex justify-between items-center px-2">
        <div class="flex items-center space-x-2 text-[11px] text-slate-400 font-semibold uppercase tracking-wider">
            <span class="relative flex h-2 w-2">
                <span id="table-sync-indicator" class="absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span id="table-sync-status">Connecting...</span>
        </div>
        <button type="button" onclick="fetchSubmissions()" class="text-xs text-slate-400 hover:text-white flex items-center space-x-1 focus:outline-none transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            <span>Refresh Table</span>
        </button>
    </div>

    <!-- Data Table Card -->
    <div class="glass-panel rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/10 bg-slate-950/40 text-[10px] text-slate-400 uppercase font-extrabold tracking-wider">
                        <th class="px-6 py-4 cursor-pointer hover:text-emerald-400 transition-colors" data-sortable data-col="student_name" onclick="handleSort('student_name')">
                            Student Info<span class="sort-indicator"></span>
                        </th>
                        <th class="px-6 py-4 cursor-pointer hover:text-emerald-400 transition-colors" data-sortable data-col="department" onclick="handleSort('department')">
                            Department<span class="sort-indicator"></span>
                        </th>
                        <th class="px-6 py-4 cursor-pointer hover:text-emerald-400 transition-colors" data-sortable data-col="semester" onclick="handleSort('semester')">
                            Semester<span class="sort-indicator"></span>
                        </th>
                        <th class="px-6 py-4 cursor-pointer hover:text-emerald-400 transition-colors" data-sortable data-col="created_at" onclick="handleSort('created_at')">
                            Upload Time<span class="sort-indicator"> ↓</span>
                        </th>
                        <th class="px-6 py-4 text-right">PDF Actions</th>
                    </tr>
                </thead>
                <tbody id="submissions-table-body" class="divide-y divide-white/5">
                    <!-- Dynamic rows loaded by admin.js -->
                    <tr>
                        <td colspan="5" class="py-12 text-center">
                            <div class="flex justify-center items-center">
                                <div class="loader-spinner"></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- CSRF Token Container for deletions -->
<input type="hidden" id="table-csrf-token" value="<?php echo csrf_token(); ?>">

<!-- Confirmation Modal for Delete Action -->
<div id="delete-confirm-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    
    <!-- Card -->
    <div class="relative w-full max-w-md rounded-2xl bg-slate-900 border border-white/10 shadow-glass overflow-hidden transform scale-95 transition-all duration-300 flex flex-col z-10 p-6 space-y-6">
        <div class="flex items-center space-x-3 text-rose-400">
            <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
            <h3 class="text-lg font-outfit font-bold text-white">Delete Submission?</h3>
        </div>
        
        <p class="text-xs text-slate-400 leading-relaxed">
            Are you sure you want to permanently delete the submission of <strong id="delete-target-name" class="text-white">Student Name</strong>? This will remove their record from the database and permanently delete their uploaded PDF file from the server disk. This action is irreversible.
        </p>
        
        <div class="flex justify-end space-x-2 pt-2">
            <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 rounded-lg text-xs font-semibold bg-white/5 hover:bg-white/10 text-white transition-colors focus:outline-none">
                Cancel
            </button>
            <button type="button" onclick="executeDeleteSubmission()" class="px-4 py-2 rounded-lg text-xs font-semibold bg-rose-600 hover:bg-rose-500 text-white transition-colors focus:outline-none">
                Confirm Delete
            </button>
        </div>
    </div>
</div>

<!-- Load administrative controls -->
<script src="<?php echo BASE_URL; ?>/assets/js/admin.js"></script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
