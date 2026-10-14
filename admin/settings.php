<?php
/**
 * Bangladesh Students Community Contest Platform - Settings Panel
 */

require_once dirname(__DIR__) . '/includes/auth.php';

// Enforce admin login
require_login();

$page_title = "Admin Settings";
require_once dirname(__DIR__) . '/includes/header.php';

// Fetch current configurations
$contest_title = get_setting('contest_title', 'Bangladesh Students Community');
$contest_topic = get_setting('contest_topic', 'আমি যা হতে চেয়েছিলাম এবং যা হতে যাচ্ছি');
$contest_deadline = get_setting('contest_deadline', '2026-06-30 23:59:59');
$is_active = (int)get_setting('is_contest_active', '1');
$max_size_bytes = (int)get_setting('max_upload_size', MAX_FILE_SIZE_BYTES);

// Format bytes to MB for display in select input
$max_size_mb = round($max_size_bytes / (1024 * 1024), 0);

// Convert mysql timestamp format to datetime-local format (Y-m-d\TH:i)
$datetime_local = date('Y-m-d\TH:i', strtotime($contest_deadline));
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    
    <!-- Admin Control Sub-Navigation Bar -->
    <div class="glass-panel rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-md">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.43l-1.003.828c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.43l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.645-.869L9.594 3.94z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div>
                <h1 class="font-outfit text-xl font-bold text-white leading-tight">Settings & Maintenance</h1>
                <p class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Configure contest rules and administrative options</p>
            </div>
        </div>
        
        <!-- Controls -->
        <div class="flex flex-wrap items-center justify-center gap-2">
            <a href="index.php" class="px-4 py-2 rounded-lg text-xs font-semibold bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white transition-colors">
                Overview
            </a>
            <a href="submissions.php" class="px-4 py-2 rounded-lg text-xs font-semibold bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white transition-colors">
                Submissions
            </a>
            <a href="settings.php" class="px-4 py-2 rounded-lg text-xs font-semibold bg-emerald-500 text-darkbg hover:bg-emerald-400 transition-colors shadow-sm">
                Settings
            </a>
            <span class="w-[1px] h-5 bg-white/10 mx-1"></span>
            <a href="logout.php" class="px-4 py-2 rounded-lg text-xs font-semibold bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 hover:border-rose-500/30 transition-all duration-200">
                Sign Out
            </a>
        </div>
    </div>

    <!-- Double Column Settings Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Column 1 Panel: Contest Configurations -->
        <div class="glass-panel rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-white/5 pb-3">
                <h3 class="font-outfit font-bold text-white text-base tracking-wide flex items-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-2.5"></span> Contest Parameters
                </h3>
                <p class="text-[10px] text-slate-400">Manage titles, deadlines and submission activity</p>
            </div>

            <form id="contest-settings-form" action="../api/settings.php" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="update_contest">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                <!-- Contest Title -->
                <div class="space-y-1.5">
                    <label for="contest_title" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Contest Platform Title</label>
                    <input type="text" name="contest_title" id="contest_title" value="<?php echo esc($contest_title); ?>" required
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950/40 border border-white/10 text-white text-xs focus:outline-none transition-all duration-200">
                </div>

                <!-- Contest Topic -->
                <div class="space-y-1.5">
                    <label for="contest_topic" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Contest Topic (Bangla)</label>
                    <input type="text" name="contest_topic" id="contest_topic" value="<?php echo esc($contest_topic); ?>" required
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950/40 border border-white/10 text-white text-xs font-bangla focus:outline-none transition-all duration-200">
                </div>

                <!-- Deadline and Size row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Deadline datetime picker -->
                    <div class="space-y-1.5">
                        <label for="contest_deadline" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Submission Deadline</label>
                        <input type="datetime-local" name="contest_deadline" id="contest_deadline" value="<?php echo $datetime_local; ?>" required
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-950/40 border border-white/10 text-white text-xs focus:outline-none transition-all duration-200">
                    </div>

                    <!-- Max File Size select -->
                    <div class="space-y-1.5">
                        <label for="max_upload_size" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Max File Size Limit</label>
                        <select name="max_upload_size" id="max_upload_size" required
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-950/40 border border-white/10 text-white text-xs focus:outline-none cursor-pointer">
                            <option value="10" <?php echo $max_size_mb == 10 ? 'selected' : ''; ?>>10 Megabytes (10MB)</option>
                            <option value="25" <?php echo $max_size_mb == 25 ? 'selected' : ''; ?>>25 Megabytes (25MB)</option>
                            <option value="50" <?php echo $max_size_mb == 50 ? 'selected' : ''; ?>>50 Megabytes (50MB)</option>
                            <option value="100" <?php echo $max_size_mb == 100 ? 'selected' : ''; ?>>100 Megabytes (100MB)</option>
                        </select>
                    </div>
                </div>

                <!-- Active toggle -->
                <div class="flex items-center space-x-3 py-2">
                    <input type="checkbox" name="is_contest_active" id="is_contest_active" value="1" <?php echo $is_active ? 'checked' : ''; ?>
                           class="w-4.5 h-4.5 rounded bg-slate-950/40 border-white/10 text-emerald-500 focus:ring-emerald-500 cursor-pointer">
                    <label for="is_contest_active" class="text-xs font-semibold text-slate-300 cursor-pointer select-none">
                        Enable uploads (Contest Active)
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white font-bold text-xs tracking-wider transition-all duration-300 btn-glow-emerald">
                    Save Contest Settings
                </button>
            </form>
        </div>

        <!-- Column 1 Panel: Admin Credentials -->
        <div class="glass-panel rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-white/5 pb-3">
                <h3 class="font-outfit font-bold text-white text-base tracking-wide flex items-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-teal-500 mr-2.5"></span> Administrator Credentials
                </h3>
                <p class="text-[10px] text-slate-400">Update system login security username and password</p>
            </div>

            <form id="credential-settings-form" action="../api/settings.php" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="change_credentials">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                <!-- New Username -->
                <div class="space-y-1.5">
                    <label for="new_username" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">New Username</label>
                    <input type="text" name="new_username" id="new_username" placeholder="Enter new username" required
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950/40 border border-white/10 text-white text-xs focus:outline-none transition-all duration-200">
                </div>

                <!-- Current Password -->
                <div class="space-y-1.5">
                    <label for="current_password" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Verify Current Password</label>
                    <input type="password" name="current_password" id="current_password" placeholder="••••••••" required
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950/40 border border-white/10 text-white text-xs focus:outline-none transition-all duration-200">
                </div>

                <!-- New Password -->
                <div class="space-y-1.5">
                    <label for="new_password" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">New Password</label>
                    <input type="password" name="new_password" id="new_password" placeholder="•••••••• (Min 6 characters)" required
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950/40 border border-white/10 text-white text-xs focus:outline-none transition-all duration-200">
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-400 hover:to-teal-500 text-white font-bold text-xs tracking-wider transition-all duration-300">
                    Change Admin Credentials
                </button>
            </form>
        </div>

        <!-- Column 2 Panel: Data Portability (Export/Import) -->
        <div class="glass-panel rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
            <div class="border-b border-white/5 pb-3">
                <h3 class="font-outfit font-bold text-white text-base tracking-wide flex items-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-400 mr-2.5"></span> Data Backups & Portability
                </h3>
                <p class="text-[10px] text-slate-400">Download complete dataset files or restore backup states</p>
            </div>

            <!-- Export Grid -->
            <div class="space-y-3">
                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Export Panel</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Export CSV -->
                    <a href="../api/settings.php?action=export_csv&csrf_token=<?php echo csrf_token(); ?>"
                       class="p-4 rounded-xl bg-slate-950/20 hover:bg-slate-950/40 border border-white/5 text-center flex flex-col items-center justify-center space-y-2 group transition-colors">
                        <span class="text-2xl group-hover:scale-110 transition-transform">📊</span>
                        <span class="text-xs font-bold text-white">Export to CSV</span>
                        <span class="text-[9px] text-slate-500">Excel / Table layout sheet</span>
                    </a>

                    <!-- Backup SQL -->
                    <a href="../api/settings.php?action=backup_db&csrf_token=<?php echo csrf_token(); ?>"
                       class="p-4 rounded-xl bg-slate-950/20 hover:bg-slate-950/40 border border-white/5 text-center flex flex-col items-center justify-center space-y-2 group transition-colors">
                        <span class="text-2xl group-hover:scale-110 transition-transform">💾</span>
                        <span class="text-xs font-bold text-white">Backup Database</span>
                        <span class="text-[9px] text-slate-500">Pure SQL Schema & Data Dump</span>
                    </a>
                </div>
            </div>

            <!-- Import form -->
            <div class="space-y-3 pt-3 border-t border-white/5">
                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Import Panel (JSON)</h4>
                
                <form id="import-json-form" action="../api/settings.php" method="POST" enctype="multipart/form-data" class="space-y-3">
                    <input type="hidden" name="action" value="import_json">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    
                    <div class="flex items-center space-x-2">
                        <input type="file" name="import_file" id="import_file" accept=".json" required
                               class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-white/5 file:text-slate-300 hover:file:bg-white/10 file:cursor-pointer">
                    </div>
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-slate-950/50 hover:bg-slate-950 border border-white/10 hover:border-white/20 text-white font-bold text-xs transition-colors">
                        Process JSON Import
                    </button>
                </form>
            </div>
        </div>

        <!-- Column 2 Panel: Danger Zone -->
        <div class="glass-panel rounded-2xl p-6 sm:p-8 shadow-sm space-y-6 border-rose-500/20">
            <div class="border-b border-rose-500/10 pb-3">
                <h3 class="font-outfit font-bold text-rose-400 text-base tracking-wide flex items-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 mr-2.5"></span> Danger Zone
                </h3>
                <p class="text-[10px] text-rose-500/60">Dangerous maintenance operations. Handle with extreme caution</p>
            </div>

            <div class="space-y-4">
                <div class="text-xs text-slate-400 leading-relaxed space-y-2">
                    <p>Executing a system reset will:</p>
                    <ul class="list-disc list-inside space-y-1 text-slate-500">
                        <li>Permanently <strong class="text-rose-300">TRUNCATE</strong> the submissions database table.</li>
                        <li>Delete all uploaded contest entry <strong class="text-rose-300">PDF files</strong> from the host disk.</li>
                        <li>This action cannot be undone. Backup your data before proceeding!</li>
                    </ul>
                </div>

                <button type="button" onclick="openPurgeModal()" class="w-full py-3 rounded-xl bg-rose-950/30 hover:bg-rose-600 border border-rose-500/20 hover:border-rose-500 text-rose-400 hover:text-white font-bold text-xs tracking-wider transition-all duration-300 btn-glow-rose">
                    Delete All Submissions & Uploads
                </button>
            </div>
        </div>

    </div>
</div>

<!-- factory reset purge modal -->
<div id="purge-confirm-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm" onclick="closePurgeModal()"></div>
    
    <!-- Modal Box -->
    <form id="purge-data-form" action="../api/settings.php" method="POST"
          class="relative w-full max-w-md rounded-2xl bg-slate-900 border border-rose-500/35 shadow-glass overflow-hidden transform scale-95 transition-all duration-300 flex flex-col z-10 p-6 space-y-6">
        <input type="hidden" name="action" value="clear_data">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        
        <div class="flex items-center space-x-3 text-rose-400">
            <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
            <h3 class="text-lg font-outfit font-bold text-white">Factory Reset Platform?</h3>
        </div>
        
        <div class="space-y-3">
            <p class="text-xs text-slate-400 leading-relaxed">
                Confirming this action will purge all submissions and remove PDFs from disk storage. 
                Please type the exact phrase <strong class="text-white select-none">DELETE_ALL_SUBMISSIONS</strong> in the box below to authorize:
            </p>
            <input type="text" name="confirm_delete_all" id="confirm_delete_all" autocomplete="off" required placeholder="Type the phrase to authorize"
                   class="w-full px-4 py-2.5 rounded-xl bg-slate-950/40 border border-rose-500/25 text-white text-xs focus:outline-none focus:border-rose-500 transition-all duration-200">
        </div>
        
        <div class="flex justify-end space-x-2">
            <button type="button" onclick="closePurgeModal()" class="px-4 py-2 rounded-lg text-xs font-semibold bg-white/5 hover:bg-white/10 text-white transition-colors focus:outline-none">
                Cancel
            </button>
            <button type="submit" class="px-4 py-2 rounded-lg text-xs font-semibold bg-rose-600 hover:bg-rose-500 text-white transition-colors focus:outline-none">
                Yes, Purge Everything
            </button>
        </div>
    </form>
</div>

<!-- Settings AJAX and Modal Scripts -->
<script>
function openPurgeModal() {
    const modal = document.getElementById('purge-confirm-modal');
    if (modal) {
        modal.classList.remove('pointer-events-none', 'opacity-0');
        modal.querySelector('.relative, form').classList.remove('scale-95');
        document.getElementById('confirm_delete_all').value = '';
    }
}

function closePurgeModal() {
    const modal = document.getElementById('purge-confirm-modal');
    if (modal) {
        modal.classList.add('pointer-events-none', 'opacity-0');
        modal.querySelector('.relative, form').classList.add('scale-95');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // 1. Contest settings save handler
    const contestForm = document.getElementById('contest-settings-form');
    if (contestForm) {
        contestForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const submitBtn = contestForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving Settings...';
            
            fetch(contestForm.getAttribute('action'), {
                method: 'POST',
                body: new FormData(contestForm)
            })
            .then(res => res.json())
            .then(res => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Contest Settings';
                if (res.success) {
                    showToastNotification(res.message);
                } else {
                    alert(res.message || 'Error saving settings');
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Contest Settings';
                alert('Connection failure.');
            });
        });
    }

    // 2. Credentials save handler
    const credentialsForm = document.getElementById('credential-settings-form');
    if (credentialsForm) {
        credentialsForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const submitBtn = credentialsForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Updating...';
            
            fetch(credentialsForm.getAttribute('action'), {
                method: 'POST',
                body: new FormData(credentialsForm)
            })
            .then(res => res.json())
            .then(res => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Change Admin Credentials';
                if (res.success) {
                    showToastNotification(res.message);
                    credentialsForm.reset();
                } else {
                    alert(res.message || 'Error updating credentials');
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Change Admin Credentials';
                alert('Connection failure.');
            });
        });
    }

    // 3. JSON Import handler
    const importForm = document.getElementById('import-json-form');
    if (importForm) {
        importForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const submitBtn = importForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Importing Data...';
            
            fetch(importForm.getAttribute('action'), {
                method: 'POST',
                body: new FormData(importForm)
            })
            .then(res => res.json())
            .then(res => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Process JSON Import';
                if (res.success) {
                    showToastNotification(res.message);
                    importForm.reset();
                } else {
                    alert(res.message || 'Error importing JSON');
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Process JSON Import';
                alert('Connection failure.');
            });
        });
    }

    // 4. Data purge reset handler
    const purgeForm = document.getElementById('purge-data-form');
    if (purgeForm) {
        purgeForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const submitBtn = purgeForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Purging Platform...';
            
            fetch(purgeForm.getAttribute('action'), {
                method: 'POST',
                body: new FormData(purgeForm)
            })
            .then(res => res.json())
            .then(res => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Yes, Purge Everything';
                if (res.success) {
                    showToastNotification(res.message);
                    closePurgeModal();
                } else {
                    alert(res.message || 'Error purging database');
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Yes, Purge Everything';
                alert('Connection failure.');
            });
        });
    }
});
</script>

<!-- Load dashboard functions for helper notifications -->
<script src="<?php echo BASE_URL; ?>/assets/js/admin.js"></script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
