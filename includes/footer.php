<?php
/**
 * Bangladesh Students Community Contest Platform - Global Footer
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
?>
    </main>

    <!-- Global Footer Section -->
    <footer class="border-t border-white/5 bg-darkbg/80 backdrop-blur-md py-12 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Branding column -->
                <div class="flex flex-col space-y-4">
                    <div class="flex items-center space-x-3">
                        <img src="<?php echo BASE_URL; ?>/assets/images/bsc_logo.png" alt="Bangladesh Students Community" class="w-9 h-9 object-contain rounded-lg">
                        <span class="font-outfit font-bold text-white text-lg tracking-wide">Bangladesh Students Community</span>
                    </div>
                    <p class="text-xs text-slate-400 max-w-sm leading-relaxed">
                        A vibrant community platform for Bangladeshi students residing and studying at Assam down town University (ADTU), Guwahati, Assam, India. Promoting academic excellence, unity, and cultural pride.
                    </p>
                </div>
                
                <!-- Quick links -->
                <div class="flex flex-col space-y-4 md:items-center">
                    <div>
                        <h4 class="font-outfit font-semibold text-slate-200 text-sm tracking-widest uppercase mb-4">Quick Links</h4>
                        <ul class="space-y-2.5 text-xs text-slate-400">
                            <li><a href="<?php echo BASE_URL; ?>/index.php" class="hover:text-emerald-400 transition-colors">Home</a></li>
                            <li><a href="<?php echo BASE_URL; ?>/contest.php" class="hover:text-emerald-400 transition-colors">Contest Portal</a></li>
                            <li><button type="button" onclick="openHelpModal()" class="hover:text-emerald-400 transition-colors text-left focus:outline-none">Help Guide</button></li>
                            <li><a href="<?php echo BASE_URL; ?>/admin/login.php" class="hover:text-emerald-400 transition-colors">Admin Area</a></li>
                        </ul>
                    </div>
                </div>

                <!-- University Info and Contact -->
                <div class="flex flex-col space-y-4">
                    <h4 class="font-outfit font-semibold text-slate-200 text-sm tracking-widest uppercase mb-4">Location & Affiliation</h4>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        <strong>Assam down town University (ADTU)</strong><br>
                        Sankar Madhab Path, Gandhi Nagar, Panikhaiti,<br>
                        Guwahati, Assam, India - 781026
                    </p>
                    <div class="pt-2 border-t border-white/5 text-[10px] text-slate-500">
                        &copy; <?php echo date('Y'); ?> Bangladesh Students Community, ADTU. All Rights Reserved.
                    </div>
                    <div class="text-[10px] text-slate-600 mt-1">
                        Developed by <a href="https://exploitinject.dev/" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-emerald-400 transition-colors duration-200 font-medium">ExploitInject</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating Help Action Button -->
    <button type="button" onclick="openHelpModal()" class="fixed bottom-6 right-6 z-40 flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-xl shadow-emerald-950/40 hover:scale-110 active:scale-95 transition-all duration-300 group focus:outline-none" title="Help Guide">
        <!-- Floating question mark / glow effect -->
        <div class="absolute inset-0 rounded-full bg-emerald-400 opacity-0 group-hover:opacity-30 blur-md transition-opacity duration-300"></div>
        <svg class="w-7 h-7 relative z-10 transition-transform duration-300 group-hover:rotate-12" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
        </svg>
    </button>

    <!-- Beautiful Help Modal Popup -->
    <div id="help-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 opacity-0 pointer-events-none transition-all duration-300" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity duration-300" onclick="closeHelpModal()"></div>
        
        <!-- Modal Card -->
        <div class="relative w-full max-w-2xl rounded-2xl bg-slate-900/90 border border-white/10 shadow-glass overflow-hidden transform scale-95 transition-all duration-300 flex flex-col max-h-[85vh] z-10">
            <!-- Top highlight line -->
            <div class="h-1.5 w-full bg-gradient-to-r from-emerald-500 via-teal-500 to-rose-500"></div>
            
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-white/5 bg-slate-950/30">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-outfit font-bold text-white leading-tight">Contest Guidelines & Help</h3>
                        <p class="text-[10px] text-slate-400 font-medium tracking-wide">সহায়িকা ও নির্দেশনাবলী</p>
                    </div>
                </div>
                <button type="button" onclick="closeHelpModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/5 transition-colors focus:outline-none" aria-label="Close modal">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Body content (Scrollable) -->
            <div class="px-6 py-6 overflow-y-auto space-y-6 scrollbar-thin scrollbar-track-slate-800 scrollbar-thumb-slate-700">
                
                <!-- Topic Spotlight (Bangla) -->
                <div class="p-4 rounded-xl bg-gradient-to-r from-emerald-950/30 to-slate-900 border border-emerald-500/20">
                    <span class="text-xs font-semibold text-emerald-400 uppercase tracking-widest block mb-1">Contest Topic | প্রতিযোগিতার বিষয়</span>
                    <h4 class="text-lg font-bold font-bangla text-white">"আমি যা হতে চেয়েছিলাম এবং যা হতে যাচ্ছি"</h4>
                </div>

                <!-- Rules Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Column 1: Rules & Submissions -->
                    <div class="space-y-4">
                        <h5 class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-2"></span> Rules (নিয়ামাবলী)
                        </h5>
                        <ul class="space-y-2.5 text-xs text-slate-400 list-disc list-inside">
                            <li>Entries must be submission-ready and fully written by the participant.</li>
                            <li>Only <strong class="text-slate-200">PDF format</strong> uploads are accepted.</li>
                            <li>Maximum file size is <strong class="text-slate-200">100MB</strong>.</li>
                            <li>Only one submission per student is permitted.</li>
                            <li>Ensure your Name, Department, and Semester match your official ADTU records.</li>
                        </ul>
                    </div>

                    <!-- Column 2: How to create PDF -->
                    <div class="space-y-4">
                        <h5 class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2"></span> How to Create PDF
                        </h5>
                        <div class="space-y-3 text-xs text-slate-400">
                            <div class="flex items-start space-x-2">
                                <span class="bg-white/5 text-slate-300 rounded px-1.5 py-0.5 font-bold font-mono text-[10px]">1</span>
                                <span><strong>Microsoft Word / WPS Office:</strong> Write your essay, click <em>File -> Save As</em> and choose <strong>PDF Format (*.pdf)</strong>.</span>
                            </div>
                            <div class="flex items-start space-x-2">
                                <span class="bg-white/5 text-slate-300 rounded px-1.5 py-0.5 font-bold font-mono text-[10px]">2</span>
                                <span><strong>Google Docs:</strong> Open document, click <em>File -> Download -> PDF Document (.pdf)</em>.</span>
                            </div>
                            <div class="flex items-start space-x-2">
                                <span class="bg-white/5 text-slate-300 rounded px-1.5 py-0.5 font-bold font-mono text-[10px]">3</span>
                                <span><strong>Handwritten (Scanner):</strong> Write neatly, capture pages using <em>Adobe Scan</em>, <em>CamScanner</em>, or similar, and export/share as PDF.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step-by-Step Upload Instructions -->
                <div class="border-t border-white/5 pt-5 space-y-3">
                    <h5 class="text-xs font-bold text-slate-300 uppercase tracking-wider">How to Upload (জমা দেওয়ার নিয়ম)</h5>
                    <ol class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <li class="p-3.5 rounded-lg bg-slate-950/20 border border-white/5 flex flex-col justify-between">
                            <span class="text-emerald-400 font-bold mb-1">01. Fill Info</span>
                            <span class="text-slate-400">Enter your full name, choose department, and select semester.</span>
                        </li>
                        <li class="p-3.5 rounded-lg bg-slate-950/20 border border-white/5 flex flex-col justify-between">
                            <span class="text-emerald-400 font-bold mb-1">02. Attach PDF</span>
                            <span class="text-slate-400">Click the upload area and pick your formatted PDF essay file.</span>
                        </li>
                        <li class="p-3.5 rounded-lg bg-slate-950/20 border border-white/5 flex flex-col justify-between">
                            <span class="text-emerald-400 font-bold mb-1">03. Submit Entry</span>
                            <span class="text-slate-400">Click submit. A loader will verify files, followed by a success screen.</span>
                        </li>
                    </ol>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="px-6 py-4 border-t border-white/5 bg-slate-950/30 flex justify-end">
                <button type="button" onclick="closeHelpModal()" class="px-5 py-2 rounded-lg text-xs font-semibold bg-white/5 hover:bg-white/10 text-white transition-colors focus:outline-none">
                    Understood (ঠিক আছে)
                </button>
            </div>
        </div>
    </div>

    <!-- Script imports -->
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
