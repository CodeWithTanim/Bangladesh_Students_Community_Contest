<?php
/**
 * Bangladesh Students Community Contest Platform - Contest Upload Portal
 */

$page_title = "Contest Submission";
require_once __DIR__ . '/includes/header.php';

// Fetch upload settings
$is_active = (int)get_setting('is_contest_active', '1');
$contest_deadline = get_setting('contest_deadline', '2026-06-30 23:59:59');
$max_size = (int)get_setting('max_upload_size', MAX_FILE_SIZE_BYTES);

$deadline_timestamp = strtotime($contest_deadline);
$is_closed = (time() > $deadline_timestamp) || !$is_active;
$formatted_max_size = round($max_size / (1024 * 1024), 0);
?>

<section class="py-16 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
    <div class="space-y-8">
        <!-- Breadcrumb / Header -->
        <div class="text-center space-y-2">
            <h1 class="font-outfit text-3xl sm:text-4xl font-extrabold text-white">Contest Entry Submission</h1>
            <p class="text-xs sm:text-sm text-slate-400 font-medium">নিচের ফর্মে সঠিক তথ্য দিয়ে আপনার প্রতিযোগিতার ফাইলটি (PDF) জমা দিন</p>
            <div class="w-16 h-1 bg-emerald-500 mx-auto rounded-full mt-3"></div>
        </div>

        <?php if ($is_closed): ?>
            <!-- Submission Closed State -->
            <div class="glass-panel rounded-2xl p-8 text-center space-y-6 shadow-glass border-rose-500/20">
                <div class="w-16 h-16 rounded-full bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-500 text-3xl mx-auto">🔒</div>
                <div class="space-y-2">
                    <h2 class="text-xl font-bold text-white">Submissions are Closed</h2>
                    <p class="text-sm text-slate-400 max-w-md mx-auto">
                        The contest deadline has passed or submissions have been temporarily disabled by the administrator. Thank you for your interest!
                    </p>
                </div>
                <a href="<?php echo BASE_URL; ?>/index.php" class="inline-flex px-6 py-2.5 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10 text-white text-xs font-semibold transition-colors">
                    Back to Home
                </a>
            </div>
        <?php else: ?>
            <!-- Main Contest Form -->
            <div id="contest-upload-card" class="glass-panel rounded-2xl p-6 sm:p-10 shadow-glass border-white/5 relative">
                <!-- Alert box for frontend error reporting -->
                <div id="form-alert-box" class="hidden mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/25 text-rose-300 text-xs flex items-start space-x-2">
                    <svg class="w-4 h-4 text-rose-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <span id="form-alert-text"></span>
                </div>

                <form id="contest-upload-form" action="<?php echo BASE_URL; ?>/api/submit.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <!-- CSRF Protection Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Student Name -->
                        <div class="space-y-2">
                            <label for="student_name" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Student Name (পূর্ণ নাম)</label>
                            <input type="text" name="student_name" id="student_name" required placeholder="Enter your full name" 
                                   class="w-full px-4 py-3 rounded-xl bg-slate-950/40 border border-white/10 text-white text-sm placeholder-slate-500 focus:outline-none transition-all duration-200">
                        </div>

                        <!-- Department -->
                        <div class="space-y-2">
                            <label for="department" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Department (বিভাগ)</label>
                            <input type="text" name="department" id="department" required placeholder="e.g. CSE, Pharmacy, BBA" 
                                   class="w-full px-4 py-3 rounded-xl bg-slate-950/40 border border-white/10 text-white text-sm placeholder-slate-500 focus:outline-none transition-all duration-200">
                        </div>
                    </div>

                    <!-- Semester Select -->
                    <div class="space-y-2">
                        <label for="semester" class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Semester (সেমিস্টার)</label>
                        <select name="semester" id="semester" required 
                                class="w-full px-4 py-3 rounded-xl bg-slate-950/40 border border-white/10 text-white text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all duration-200 appearance-none cursor-pointer">
                            <option value="" disabled selected class="bg-slate-900 text-slate-500">Select your current semester</option>
                            <?php for ($i = 1; $i <= 8; $i++): ?>
                                <option value="Semester <?php echo $i; ?>" class="bg-slate-900 text-white">Semester <?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <!-- File Drag Area -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Upload Essay File (পিডিএফ ফাইল)</label>
                        
                        <div id="file-drag-area" class="w-full border-2 border-dashed border-white/10 hover:border-emerald-500/50 rounded-2xl p-8 flex flex-col items-center justify-center space-y-3 cursor-pointer transition-all duration-300 relative bg-slate-950/20">
                            <input type="file" name="pdf_file" id="pdf_file" accept=".pdf" class="hidden">
                            
                            <!-- Static drag prompt -->
                            <div id="select-file-prompt" class="flex flex-col items-center space-y-2">
                                <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-slate-400 group-hover:text-emerald-400 transition-colors">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-white">Drag & drop your PDF here, or <span class="text-emerald-400">browse</span></p>
                                <p class="text-[10px] text-slate-500 uppercase tracking-wide">PDF Only, max size <?php echo $formatted_max_size; ?>MB</p>
                            </div>

                            <!-- Selected File details display -->
                            <div id="file-details-box" class="hidden w-full flex items-center justify-between p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                                <div class="flex items-center space-x-3 min-w-0">
                                    <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400 flex-shrink-0 font-bold text-xs uppercase">PDF</div>
                                    <div class="min-w-0 flex flex-col">
                                        <span id="selected-file-name" class="text-xs font-semibold text-white truncate max-w-[250px] sm:max-w-[400px]">document.pdf</span>
                                        <span id="selected-file-size" class="text-[10px] text-slate-400">0.00 MB</span>
                                    </div>
                                </div>
                                <button type="button" onclick="event.stopPropagation(); document.getElementById('pdf_file').value = ''; document.getElementById('contest-upload-form').dispatchEvent(new Event('change'));" class="text-slate-400 hover:text-rose-400 text-xs px-2.5 py-1 rounded-md hover:bg-rose-500/10 transition-colors">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Submit action button -->
                    <button type="submit" id="submit-btn" class="w-full py-4 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-bold text-sm tracking-wide transition-all duration-300 btn-glow-emerald flex items-center justify-center space-x-2">
                        <span id="btn-submit-text">Submit Contest Entry</span>
                        <div id="btn-submit-loader" class="hidden loader-spinner"></div>
                    </button>
                </form>
            </div>
            
            <!-- Hidden Success Card (Transitions in via AJAX call) -->
            <div id="contest-success-card" class="hidden glass-panel rounded-2xl p-8 sm:p-12 text-center space-y-6 shadow-glass border-emerald-500/20">
                <div class="w-20 h-20 rounded-full bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-4xl mx-auto shadow-lg shadow-emerald-500/10">
                    🎉
                </div>
                <div class="space-y-2">
                    <h2 class="text-2xl font-outfit font-extrabold text-white">Entry Submitted Successfully!</h2>
                    <p class="text-xs text-slate-400 font-medium">রচনাবলী সফলভাবে গৃহীত হয়েছে!</p>
                </div>
                <div class="max-w-md mx-auto p-4 rounded-xl bg-slate-950/40 border border-white/5 text-sm space-y-1.5">
                    <div class="flex justify-between"><span class="text-slate-500">Student Name:</span> <strong id="success-student-name" class="text-white"></strong></div>
                    <div class="flex justify-between"><span class="text-slate-500">Contest Topic:</span> <span class="text-emerald-400 font-bangla font-semibold">"আমি যা হতে চেয়েছিলাম এবং যা হতে যাচ্ছি"</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">Submission Time:</span> <span class="text-slate-300">Just Now</span></div>
                </div>
                <p class="text-xs text-slate-400 max-w-sm mx-auto leading-relaxed">
                    Thank you for participating! The admin team will review your submission details. Keep checking the homepage for winner announcements.
                </p>
                <div class="pt-4 flex flex-col sm:flex-row justify-center gap-3">
                    <a href="<?php echo BASE_URL; ?>/index.php" class="px-6 py-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20 text-white font-semibold text-xs transition-all duration-300">
                        Back to Home
                    </a>
                    <button type="button" onclick="window.location.reload();" class="px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-semibold text-xs transition-all duration-300 shadow-sm">
                        Submit Another Entry
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Additional script to hook the remove-file click -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('pdf_file');
    const form = document.getElementById('contest-upload-form');
    
    if (fileInput && form) {
        form.addEventListener('change', (e) => {
            if (e.target === fileInput && !fileInput.files.length) {
                // If the file was cleared, trigger UI update
                const selectFilePrompt = document.getElementById('select-file-prompt');
                const fileDetailsBox = document.getElementById('file-details-box');
                const fileArea = document.getElementById('file-drag-area');
                
                selectFilePrompt.classList.remove('hidden');
                fileDetailsBox.classList.add('hidden');
                fileArea.classList.remove('border-emerald-500/40', 'bg-emerald-500/5');
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
