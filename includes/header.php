<?php
/**
 * Bangladesh Students Community Contest Platform - Global Header
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? esc($page_title) . " | " : ""; ?>Bangladesh Students Community</title>
    <meta name="description" content="Contest Submission Portal for Bangladeshi Students at Assam down town University, India.">
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/bsc_logo.png">
    <link rel="shortcut icon" type="image/png" href="<?php echo BASE_URL; ?>/assets/images/bsc_logo.png">
    <link rel="apple-touch-icon" href="<?php echo BASE_URL; ?>/assets/images/bsc_logo.png">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        bd: {
                            green: '#042f2e',      // Deep forest emerald
                            lightgreen: '#065f46', // Rich green
                            emerald: '#10b981',    // Bright green accent
                            red: '#7f1d1d',        // Deep premium red
                            lightred: '#b91c1c',   // Vivid crimson
                            rose: '#f43f5e'        // Bright rose accent
                        },
                        darkbg: '#090d16',         // Premium dark background
                        cardbg: 'rgba(17, 24, 39, 0.7)',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                        bangla: ['Hind Siliguri', 'sans-serif']
                    },
                    boxShadow: {
                        'glass': '0 8px 32px 0 rgba(0, 0, 0, 0.37)',
                        'glass-accent': '0 8px 32px 0 rgba(16, 185, 129, 0.15)'
                    }
                }
            }
        }
    </script>
    
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body class="bg-darkbg text-slate-200 font-sans min-h-full flex flex-col relative overflow-x-hidden">
    <!-- Premium background elements -->
    <div class="fixed inset-0 z-[-1] overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-bd-green/20 blur-[120px] animate-pulse" style="animation-duration: 15s;"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-bd-red/10 blur-[120px] animate-pulse" style="animation-duration: 20s;"></div>
        <div class="absolute top-[30%] right-[20%] w-[300px] h-[300px] rounded-full bg-emerald-500/5 blur-[80px]"></div>
    </div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-50 w-full backdrop-blur-md bg-darkbg/60 border-b border-white/5 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo / Community Branding -->
                <div class="flex-shrink-0">
                    <a href="<?php echo BASE_URL; ?>/index.php" class="flex items-center space-x-3 group">
                        <img src="<?php echo BASE_URL; ?>/assets/images/bsc_logo.png" alt="Bangladesh Students Community" class="w-11 h-11 object-contain rounded-xl transition-transform duration-300 group-hover:scale-105">
                        <div class="flex flex-col">
                            <span class="font-outfit text-md font-bold tracking-wide text-white group-hover:text-emerald-400 transition-colors">ADTU Bangladesh</span>
                            <span class="text-[10px] text-slate-400 font-medium tracking-widest uppercase">Student Community</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation Menu -->
                <nav class="hidden md:flex space-x-1 items-center">
                    <a href="<?php echo BASE_URL; ?>/index.php" class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 <?php echo $current_page === 'index.php' ? 'text-emerald-400 bg-white/5' : 'text-slate-300 hover:text-white hover:bg-white/5'; ?>">
                        Home
                    </a>
                    <a href="<?php echo BASE_URL; ?>/contest.php" class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 <?php echo $current_page === 'contest.php' ? 'text-emerald-400 bg-white/5' : 'text-slate-300 hover:text-white hover:bg-white/5'; ?>">
                        Contest
                    </a>
                    <button type="button" onclick="openHelpModal()" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition-all duration-200">
                        Help
                    </button>
                    <span class="w-[1px] h-5 bg-white/10 mx-2"></span>
                    <?php if (is_logged_in()): ?>
                        <a href="<?php echo BASE_URL; ?>/admin/index.php" class="px-4 py-2 rounded-lg text-sm font-semibold bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/20 transition-all duration-200 shadow-sm">
                            Dashboard
                        </a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>/admin/login.php" class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/') !== false ? 'text-emerald-400 bg-white/5' : 'text-slate-300 hover:text-white hover:bg-white/5'; ?>">
                            Admin Login
                        </a>
                    <?php endif; ?>
                </nav>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button type="button" id="mobile-menu-btn" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 focus:outline-none" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <!-- Icon Menu -->
                        <svg class="h-6 w-6" id="menu-icon-open" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <!-- Icon Close -->
                        <svg class="hidden h-6 w-6" id="menu-icon-close" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu (collapsible) -->
        <div class="hidden md:hidden border-b border-white/5 bg-darkbg/95 backdrop-blur-lg" id="mobile-menu">
            <div class="px-2 pt-2 pb-4 space-y-1 sm:px-3">
                <a href="<?php echo BASE_URL; ?>/index.php" class="block px-3 py-2.5 rounded-lg text-base font-medium <?php echo $current_page === 'index.php' ? 'text-emerald-400 bg-white/5' : 'text-slate-300 hover:text-white hover:bg-white/5'; ?>">
                    Home
                </a>
                <a href="<?php echo BASE_URL; ?>/contest.php" class="block px-3 py-2.5 rounded-lg text-base font-medium <?php echo $current_page === 'contest.php' ? 'text-emerald-400 bg-white/5' : 'text-slate-300 hover:text-white hover:bg-white/5'; ?>">
                    Contest
                </a>
                <button type="button" onclick="openHelpModal(); toggleMobileMenu();" class="block w-full text-left px-3 py-2.5 rounded-lg text-base font-medium text-slate-300 hover:text-white hover:bg-white/5">
                    Help
                </button>
                <div class="border-t border-white/5 my-2 pt-2"></div>
                <?php if (is_logged_in()): ?>
                    <a href="<?php echo BASE_URL; ?>/admin/index.php" class="block px-3 py-2.5 rounded-lg text-base font-semibold text-emerald-300 bg-emerald-600/10 border border-emerald-500/10">
                        Dashboard
                    </a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/admin/login.php" class="block px-3 py-2.5 rounded-lg text-base font-medium text-slate-300 hover:text-white hover:bg-white/5">
                        Admin Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="flex-grow">
