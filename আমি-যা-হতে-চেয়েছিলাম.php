<?php
/**
 * Bangladesh Students Community Contest Platform - Home Page
 */

$page_title = "Home";
require_once __DIR__ . '/includes/header.php';

// Fetch contest details from database dynamically
$contest_topic = get_setting('contest_topic', 'আমি যা হতে চেয়েছিলাম এবং যা হতে যাচ্ছি');
$contest_deadline = get_setting('contest_deadline', '2026-06-30 23:59:59');
$is_active = (int)get_setting('is_contest_active', '1');

// Format deadline for user presentation
$deadline_timestamp = strtotime($contest_deadline);
$formatted_deadline = date('F d, Y - h:i A', $deadline_timestamp);
?>

<!-- Hero / Showcase Section -->
<section class="relative min-h-[85vh] flex items-center justify-center py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
    <div class="max-w-5xl mx-auto text-center z-10 space-y-8">
        <!-- Badge -->
        <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold uppercase tracking-wider animate-bounce">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span>Monthly Essay Contest 2026</span>
        </div>

        <!-- Hero Headline -->
        <h1 class="font-outfit text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight text-white leading-tight">
            Bangladesh Students Community<br>
            <span class="gradient-text-emerald-rose">Assam down town University</span>
        </h1>

        <p class="max-w-2xl mx-auto text-base sm:text-lg text-slate-400 leading-relaxed">
            Welcome to the official contest platform of the Bangladeshi student community at ADTU, India. Share your thoughts, express your vision, and win exciting prizes.
        </p>

        <!-- Topic Highlight Card -->
        <div class="max-w-3xl mx-auto p-6 sm:p-8 rounded-2xl glass-panel relative group transition-all duration-300 hover:border-emerald-500/30 shadow-glass-accent">
            <div class="absolute -inset-px rounded-2xl bg-gradient-to-r from-emerald-500/10 to-rose-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
            
            <span class="text-xs font-bold text-emerald-400 uppercase tracking-widest block mb-2">Contest Topic | প্রতিযোগিতার বিষয়</span>
            <h2 class="text-2xl sm:text-4xl font-extrabold font-bangla text-white tracking-wide mb-3 leading-normal">
                "আমি যা হতে চেয়েছিলাম এবং যা হতে যাচ্ছি"
            </h2>
            <p class="text-xs sm:text-sm text-slate-400 font-medium italic">
                ("What I wanted to be and what I am going to be")
            </p>
        </div>

        <!-- Countdown Timer -->
        <div class="max-w-xl mx-auto bg-slate-950/40 border border-white/5 rounded-xl p-4 flex flex-col items-center justify-center space-y-2">
            <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">Time Remaining to Submit</span>
            <div id="countdown" data-deadline="<?php echo esc($contest_deadline); ?>" class="flex space-x-4 sm:space-x-8 text-white font-outfit">
                <div class="flex flex-col items-center">
                    <span id="days" class="text-2xl sm:text-4xl font-extrabold text-emerald-400">00</span>
                    <span class="text-[10px] text-slate-500 uppercase tracking-wider">Days</span>
                </div>
                <span class="text-2xl sm:text-4xl text-slate-700 font-bold">:</span>
                <div class="flex flex-col items-center">
                    <span id="hours" class="text-2xl sm:text-4xl font-extrabold text-emerald-400">00</span>
                    <span class="text-[10px] text-slate-500 uppercase tracking-wider">Hours</span>
                </div>
                <span class="text-2xl sm:text-4xl text-slate-700 font-bold">:</span>
                <div class="flex flex-col items-center">
                    <span id="minutes" class="text-2xl sm:text-4xl font-extrabold text-emerald-400">00</span>
                    <span class="text-[10px] text-slate-500 uppercase tracking-wider">Mins</span>
                </div>
                <span class="text-2xl sm:text-4xl text-slate-700 font-bold">:</span>
                <div class="flex flex-col items-center">
                    <span id="seconds" class="text-2xl sm:text-4xl font-extrabold text-rose-500">00</span>
                    <span class="text-[10px] text-slate-500 uppercase tracking-wider">Secs</span>
                </div>
            </div>
            <span class="text-xs text-slate-400 font-semibold pt-1">
                Deadline: <?php echo esc($formatted_deadline); ?>
            </span>
        </div>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
            <?php if ($is_active): ?>
                <a href="<?php echo BASE_URL; ?>/contest.php" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-white font-semibold text-sm transition-all duration-300 btn-glow-emerald text-center">
                    Submit Your Entry
                </a>
            <?php else: ?>
                <div class="w-full sm:w-auto px-8 py-4 rounded-xl bg-slate-800 text-slate-400 border border-white/5 font-semibold text-sm text-center cursor-not-allowed">
                    Submission Closed
                </div>
            <?php endif; ?>
            <button type="button" onclick="openHelpModal()" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-white font-semibold text-sm border border-white/10 hover:border-white/20 transition-all duration-300 text-center">
                View Submission Rules
            </button>
        </div>
    </div>
</section>

<!-- Rules & Regulations (Bangla) -->
<section class="py-20 px-4 sm:px-6 lg:px-8 border-t border-white/5 bg-slate-950/20">
    <div class="max-w-5xl mx-auto">
        <div class="text-center space-y-3 mb-16">
            <h2 class="font-outfit text-3xl sm:text-4xl font-extrabold text-white">প্রতিযোগিতার নিয়ামাবলী</h2>
            <p class="text-sm text-slate-400 font-medium">Contest Rules & Regulations</p>
            <div class="w-16 h-1 bg-emerald-500 mx-auto rounded-full"></div>
        </div>

        <!-- Rules Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 font-bangla">
            <!-- Rule 1 -->
            <div class="p-6 rounded-xl bg-slate-900/60 border border-white/5 flex items-start space-x-4">
                <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex-shrink-0 flex items-center justify-center text-emerald-400 text-lg font-bold">১</div>
                <div class="space-y-2">
                    <h3 class="text-lg font-bold text-white">যোগ্যতা</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">প্রতিযোগীকে অবশ্যই আসাম ডাউন টাউন ইউনিভার্সিটির (ADTU) একজন নিয়মিত বাংলাদেশী শিক্ষার্থী হতে হবে।</p>
                </div>
            </div>
            
            <!-- Rule 2 -->
            <div class="p-6 rounded-xl bg-slate-900/60 border border-white/5 flex items-start space-x-4">
                <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex-shrink-0 flex items-center justify-center text-emerald-400 text-lg font-bold">২</div>
                <div class="space-y-2">
                    <h3 class="text-lg font-bold text-white">রচনার বিষয়</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">রচনার মূল বিষয়বস্তু "আমি যা হতে চেয়েছিলাম এবং যা হতে যাচ্ছি" এর উপর হতে হবে। এর বাইরে যেকোনো লেখা বাতিলযোগ্য।</p>
                </div>
            </div>

            <!-- Rule 3 -->
            <div class="p-6 rounded-xl bg-slate-900/60 border border-white/5 flex items-start space-x-4">
                <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex-shrink-0 flex items-center justify-center text-emerald-400 text-lg font-bold">৩</div>
                <div class="space-y-2">
                    <h3 class="text-lg font-bold text-white">ফাইল ফরম্যাট ও সাইজ</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">ফাইলটি অবশ্যই PDF ফরম্যাটে হতে হবে এবং সর্বোচ্চ সাইজ ১০০ মেগাবাইট (100MB)। কোনো ইমেজ বা টেক্সট ফাইল গ্রহণযোগ্য নয়।</p>
                </div>
            </div>

            <!-- Rule 4 -->
            <div class="p-6 rounded-xl bg-slate-900/60 border border-white/5 flex items-start space-x-4">
                <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex-shrink-0 flex items-center justify-center text-emerald-400 text-lg font-bold">৪</div>
                <div class="space-y-2">
                    <h3 class="text-lg font-bold text-white">জমা দেওয়ার সীমা</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">প্রতিটি শিক্ষার্থী শুধুমাত্র একটি আবেদনই জমা দিতে পারবেন। একাধিক আবেদন জমা দেওয়ার চেষ্টা করলে পূর্ববর্তী আবেদন বাতিল হতে পারে।</p>
                </div>
            </div>

            <!-- Rule 5 -->
            <div class="p-6 rounded-xl bg-slate-900/60 border border-white/5 flex items-start space-x-4">
                <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex-shrink-0 flex items-center justify-center text-emerald-400 text-lg font-bold">৫</div>
                <div class="space-y-2">
                    <h3 class="text-lg font-bold text-white">মৌলিকতা</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">রচনাটি সম্পূর্ণ মৌলিক ও নিজস্ব হতে হবে। ইন্টারনেট বা অন্য কারো লেখা কপি করলে (প্লেজিয়ারিজম) সরাসরি ডিসকোয়ালিফাই করা হবে।</p>
                </div>
            </div>

            <!-- Rule 6 -->
            <div class="p-6 rounded-xl bg-slate-900/60 border border-white/5 flex items-start space-x-4">
                <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex-shrink-0 flex items-center justify-center text-emerald-400 text-lg font-bold">৬</div>
                <div class="space-y-2">
                    <h3 class="text-lg font-bold text-white">সময়সীমা</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">আগামী ৩০ জুন, ২০২৬ এর মধ্যে এই প্ল্যাটফর্মের মাধ্যমে সরাসরি ফাইল জমা দিতে হবে। নির্ধারিত সময়ের পরে কোনো আবেদন নেয়া হবে না।</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php /* ============================================================
   PRIZES SECTION — Hidden until prizes are finalized.
   To show again: remove the opening "<?php /*" and closing "* /" ?>
   ============================================================

<!-- Prizes Section -->
<section class="py-20 px-4 sm:px-6 lg:px-8 border-t border-white/5">
    <div class="max-w-5xl mx-auto">
        <div class="text-center space-y-3 mb-16">
            <h2 class="font-outfit text-3xl sm:text-4xl font-extrabold text-white">Contest Prizes</h2>
            <p class="text-sm text-slate-400 font-medium">পুরস্কার ও সম্মাননা</p>
            <div class="w-16 h-1 bg-rose-500 mx-auto rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
            <!-- 2nd Place -->
            <div class="p-6 rounded-2xl bg-slate-900/40 border border-white/5 text-center flex flex-col items-center space-y-4 order-2 md:order-1 hover:border-white/10 transition-colors">
                <div class="w-16 h-16 rounded-full bg-slate-500/10 flex items-center justify-center text-slate-300 border border-slate-500/20 text-3xl font-bold shadow-lg">🥈</div>
                <div>
                    <h3 class="font-outfit text-xl font-bold text-white">Second Prize</h3>
                    <p class="text-xs text-slate-400 font-medium uppercase mt-1">২য় স্থান</p>
                </div>
                <div class="text-slate-300 font-semibold text-sm">Attractive Gift & Crest</div>
                <div class="text-slate-400 text-xs">Plus Certificate of Excellence</div>
            </div>

            <!-- 1st Place (Featured) -->
            <div class="p-8 rounded-2xl bg-gradient-to-b from-slate-900 to-slate-950 border border-emerald-500/30 text-center flex flex-col items-center space-y-5 order-1 md:order-2 shadow-glass relative transform scale-105 btn-glow-emerald">
                <div class="absolute top-0 transform -translate-y-1/2 px-4 py-1 rounded-full bg-emerald-500 text-darkbg text-[10px] font-extrabold uppercase tracking-widest">Champion</div>
                <div class="w-20 h-20 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-400 border border-emerald-500/20 text-4xl font-bold shadow-lg">🏆</div>
                <div>
                    <h3 class="font-outfit text-2xl font-bold text-white">First Prize</h3>
                    <p class="text-xs text-emerald-400 font-medium uppercase mt-1">১ম স্থান</p>
                </div>
                <div class="text-white font-bold text-lg">Grand Champion Trophy</div>
                <div class="text-slate-300 text-sm font-medium">Cash Prize + Premium Books Set</div>
                <div class="text-slate-400 text-xs">Plus Framed Certificate of Excellence</div>
            </div>

            <!-- 3rd Place -->
            <div class="p-6 rounded-2xl bg-slate-900/40 border border-white/5 text-center flex flex-col items-center space-y-4 order-3 md:order-3 hover:border-white/10 transition-colors">
                <div class="w-16 h-16 rounded-full bg-orange-500/10 flex items-center justify-center text-orange-400 border border-orange-500/20 text-3xl font-bold shadow-lg">🥉</div>
                <div>
                    <h3 class="font-outfit text-xl font-bold text-white">Third Prize</h3>
                    <p class="text-xs text-slate-400 font-medium uppercase mt-1">৩য় স্থান</p>
                </div>
                <div class="text-slate-300 font-semibold text-sm">Honorable Crest & Notebook</div>
                <div class="text-slate-400 text-xs">Plus Certificate of Excellence</div>
            </div>
        </div>

        <div class="text-center mt-12 text-xs text-slate-500">
            * All participants will receive a digital <strong>Certificate of Participation</strong> from the Bangladesh Students Community, ADTU.
        </div>
    </div>
</section>

*/ ?>

<!-- Inline Javascript Countdown Logic -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const countdownEl = document.getElementById('countdown');
    if (!countdownEl) return;
    
    const deadlineStr = countdownEl.getAttribute('data-deadline');
    const deadlineTime = new Date(deadlineStr).getTime();
    
    const daysEl = document.getElementById('days');
    const hoursEl = document.getElementById('hours');
    const minutesEl = document.getElementById('minutes');
    const secondsEl = document.getElementById('seconds');
    
    function updateCountdown() {
        const now = new Date().getTime();
        const diff = deadlineTime - now;
        
        if (diff <= 0) {
            daysEl.textContent = '00';
            hoursEl.textContent = '00';
            minutesEl.textContent = '00';
            secondsEl.textContent = '00';
            
            const submitCTA = document.querySelector('a[href*="contest.php"]');
            if (submitCTA) {
                submitCTA.outerHTML = `<div class="w-full sm:w-auto px-8 py-4 rounded-xl bg-slate-800 text-slate-400 border border-white/5 font-semibold text-sm text-center cursor-not-allowed">Submission Closed</div>`;
            }
            return;
        }
        
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
        
        daysEl.textContent = String(days).padStart(2, '0');
        hoursEl.textContent = String(hours).padStart(2, '0');
        minutesEl.textContent = String(minutes).padStart(2, '0');
        secondsEl.textContent = String(seconds).padStart(2, '0');
    }
    
    updateCountdown();
    setInterval(updateCountdown, 1000);
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
