<?php
/**
 * Bangladesh Students Community Contest Platform - Home Page
 */

$page_title = "Home";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero / Showcase Section -->
<section class="relative min-h-[85vh] flex items-center justify-center py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
    <div class="max-w-5xl mx-auto text-center z-10 space-y-8">
        
        <!-- Badge -->
        <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-semibold uppercase tracking-wider">
            <span class="w-2 h-2 rounded-full bg-rose-400 animate-pulse"></span>
            <span>Contest Ended</span>
        </div>

        <!-- Hero Headline -->
        <h1 class="font-outfit text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight text-white leading-tight">
            Contest Date <span class="text-rose-500">Expired</span>
        </h1>

        <p class="max-w-2xl mx-auto text-base sm:text-lg text-slate-400 leading-relaxed">
            Thank you for the overwhelming response! Please wait for the Bangladesh Students Community to host a new contest.
        </p>

        <!-- Social Links / CTA Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-8">
            <a href="https://www.facebook.com/bscadtu" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-[#1877F2]/10 hover:bg-[#1877F2]/20 border border-[#1877F2]/30 text-[#1877F2] font-semibold text-sm transition-all duration-300 flex items-center justify-center space-x-3 group">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                <span>Follow Facebook Page</span>
            </a>
            <a href="https://www.facebook.com/groups/913258625064026" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-gradient-to-r from-[#1877F2] to-[#166FE5] hover:from-[#166FE5] hover:to-[#1877F2] text-white font-semibold text-sm transition-all duration-300 shadow-lg shadow-[#1877F2]/30 flex items-center justify-center space-x-3">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12c0-5.523-4.477-10-10-10z"/></svg>
                <span>Join Facebook Group</span>
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
