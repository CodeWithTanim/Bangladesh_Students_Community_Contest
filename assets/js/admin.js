/**
 * Bangladesh Students Community Contest Platform - Admin Control Panel JS
 */

let dashboardCharts = {
    trendChart: null,
    deptChart: null,
    semesterChart: null,
    hourChart: null
};

let previousTotalCount = -1;

document.addEventListener('DOMContentLoaded', () => {
    // Determine which page is loaded
    if (document.getElementById('admin-dashboard-container')) {
        initDashboard();
    }
    if (document.getElementById('submissions-table-container')) {
        initSubmissionsTable();
    }
});

// ==========================================
// Admin Dashboard Logic
// ==========================================

function initDashboard() {
    initCharts();
    fetchStats();
    
    // Setup Realtime AJAX Polling every 5 seconds
    setInterval(fetchStats, 5000);
}

function initCharts() {
    const defaultOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    color: '#94a3b8',
                    font: { family: 'Inter', size: 11 }
                }
            }
        },
        scales: {
            x: {
                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                ticks: { color: '#94a3b8', font: { family: 'Inter', size: 10 } }
            },
            y: {
                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                ticks: { color: '#94a3b8', font: { family: 'Inter', size: 10 } }
            }
        }
    };

    // 1. Line Chart - Submission Trends
    const trendCtx = document.getElementById('trendChart')?.getContext('2d');
    if (trendCtx) {
        dashboardCharts.trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Submissions',
                    data: [],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.05)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#10b981'
                }]
            },
            options: defaultOptions
        });
    }

    // 2. Bar Chart - Departments
    const deptCtx = document.getElementById('deptChart')?.getContext('2d');
    if (deptCtx) {
        dashboardCharts.deptChart = new Chart(deptCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Entries by Dept',
                    data: [],
                    backgroundColor: 'rgba(16, 185, 129, 0.6)',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: defaultOptions
        });
    }

    // 3. Pie Chart - Semesters
    const semCtx = document.getElementById('semesterChart')?.getContext('2d');
    if (semCtx) {
        dashboardCharts.semesterChart = new Chart(semCtx, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        '#10b981', '#059669', '#047857', '#065f46',
                        '#f43f5e', '#e11d48', '#be123c', '#9f1239'
                    ],
                    borderWidth: 1,
                    borderColor: 'rgba(9, 13, 22, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: '#94a3b8',
                            font: { family: 'Inter', size: 10 }
                        }
                    }
                }
            }
        });
    }

    // 4. Histogram - Submission Hours Distribution
    const hourCtx = document.getElementById('hourChart')?.getContext('2d');
    if (hourCtx) {
        dashboardCharts.hourChart = new Chart(hourCtx, {
            type: 'bar',
            data: {
                labels: Array.from({length: 24}, (_, i) => `${String(i).padStart(2, '0')}:00`),
                datasets: [{
                    label: 'Submission Frequency (Hour)',
                    data: [],
                    backgroundColor: 'rgba(244, 63, 94, 0.55)',
                    borderColor: '#f43f5e',
                    borderWidth: 1,
                    barPercentage: 1.0,
                    categoryPercentage: 1.0
                }]
            },
            options: defaultOptions
        });
    }
}

function fetchStats() {
    const liveIndicator = document.getElementById('sync-indicator');
    if (liveIndicator) liveIndicator.classList.add('animate-ping');

    fetch('../api/stats.php')
        .then(res => {
            if (!res.ok) throw new Error('Unauthenticated or server error');
            return res.json();
        })
        .then(res => {
            if (liveIndicator) {
                setTimeout(() => liveIndicator.classList.remove('animate-ping'), 500);
            }
            if (res.success && res.data) {
                updateDashboardUI(res.data);
            }
        })
        .catch(err => {
            console.error('Stats polling failed:', err);
            const statusText = document.getElementById('sync-status-text');
            if (statusText) statusText.textContent = 'Sync Offline';
        });
}

function updateDashboardUI(data) {
    // 1. Update Total Counter
    const totalEl = document.getElementById('total-submissions-counter');
    if (totalEl) {
        // Subtle animate counter if changed
        if (previousTotalCount !== -1 && previousTotalCount !== data.total_submissions) {
            totalEl.classList.add('text-emerald-400', 'scale-110');
            setTimeout(() => totalEl.classList.remove('text-emerald-400', 'scale-110'), 1000);
            
            // Audio-free visual notification toast
            showToastNotification('New submission received!');
        }
        totalEl.textContent = data.total_submissions;
        previousTotalCount = data.total_submissions;
    }
    
    // Status text reset
    const statusText = document.getElementById('sync-status-text');
    if (statusText) statusText.textContent = 'Live Syncing';

    // 2. Update Trend Chart
    if (dashboardCharts.trendChart) {
        dashboardCharts.trendChart.data.labels = data.trend_stats.map(x => x.date_label);
        dashboardCharts.trendChart.data.datasets[0].data = data.trend_stats.map(x => x.count);
        dashboardCharts.trendChart.update();
    }

    // 3. Update Dept Chart
    if (dashboardCharts.deptChart) {
        dashboardCharts.deptChart.data.labels = data.department_stats.map(x => x.department);
        dashboardCharts.deptChart.data.datasets[0].data = data.department_stats.map(x => x.count);
        dashboardCharts.deptChart.update();
    }

    // 4. Update Semester Chart
    if (dashboardCharts.semesterChart) {
        dashboardCharts.semesterChart.data.labels = data.semester_stats.map(x => x.semester);
        dashboardCharts.semesterChart.data.datasets[0].data = data.semester_stats.map(x => x.count);
        dashboardCharts.semesterChart.update();
    }

    // 5. Update Hour Histogram Chart
    if (dashboardCharts.hourChart) {
        dashboardCharts.hourChart.data.datasets[0].data = data.hour_stats;
        dashboardCharts.hourChart.update();
    }

    // 6. Update Recent uploads list
    const listEl = document.getElementById('recent-uploads-list');
    if (listEl) {
        if (!data.recent_submissions || data.recent_submissions.length === 0) {
            listEl.innerHTML = `
                <div class="text-center py-6 text-slate-500 text-xs">
                    No submissions uploaded yet.
                </div>
            `;
            return;
        }

        let html = '';
        data.recent_submissions.forEach(sub => {
            html += `
                <div class="flex items-center justify-between p-3.5 rounded-lg bg-slate-950/20 border border-white/5 hover:border-white/10 transition-colors">
                    <div class="min-w-0 flex flex-col space-y-0.5">
                        <span class="text-xs font-bold text-white truncate max-w-[180px] sm:max-w-[280px]">${escapeHTML(sub.student_name)}</span>
                        <span class="text-[10px] text-slate-400 font-medium">${escapeHTML(sub.department)} • ${escapeHTML(sub.semester)}</span>
                    </div>
                    <span class="text-[9px] font-semibold bg-emerald-500/10 text-emerald-400 px-2 py-0.5 rounded-full whitespace-nowrap">
                        ${sub.time_ago}
                    </span>
                </div>
            `;
        });
        listEl.innerHTML = html;
    }
}

function showToastNotification(message) {
    const container = document.body;
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-24 left-6 z-50 px-4 py-3.5 rounded-xl bg-slate-900 border border-emerald-500/40 text-emerald-300 shadow-glass flex items-center space-x-3 transform translate-y-10 opacity-0 transition-all duration-300';
    toast.innerHTML = `
        <span class="flex h-2.5 w-2.5 relative">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
        </span>
        <span class="text-xs font-bold font-outfit uppercase tracking-wider">${message}</span>
    `;
    container.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-y-10', 'opacity-0');
    }, 100);
    
    // Animate out and remove
    setTimeout(() => {
        toast.classList.add('translate-y-10', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// ==========================================
// Submissions Management Table Logic
// ==========================================

let tableSubmissions = [];
let tableFilters = {
    search: '',
    department: '',
    semester: '',
    sortBy: 'created_at',
    sortOrder: 'desc'
};

function initSubmissionsTable() {
    fetchSubmissions();
    
    // Setup listeners
    const searchInput = document.getElementById('search-input');
    const deptSelect = document.getElementById('filter-dept');
    const semSelect = document.getElementById('filter-semester');
    
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            tableFilters.search = e.target.value.trim().toLowerCase();
            renderTable();
        });
    }
    if (deptSelect) {
        deptSelect.addEventListener('change', (e) => {
            tableFilters.department = e.target.value;
            renderTable();
        });
    }
    if (semSelect) {
        semSelect.addEventListener('change', (e) => {
            tableFilters.semester = e.target.value;
            renderTable();
        });
    }
    
    // Realtime Table Polling every 7 seconds
    setInterval(fetchSubmissions, 7000);
}

function fetchSubmissions() {
    const liveIndicator = document.getElementById('table-sync-indicator');
    if (liveIndicator) liveIndicator.classList.add('animate-ping');

    fetch('../api/submissions.php')
        .then(res => {
            if (!res.ok) throw new Error('Query error');
            return res.json();
        })
        .then(res => {
            if (liveIndicator) {
                setTimeout(() => liveIndicator.classList.remove('animate-ping'), 500);
            }
            if (res.success && res.data) {
                tableSubmissions = res.data;
                populateFilterDropdowns();
                renderTable();
                
                const tableStatus = document.getElementById('table-sync-status');
                if (tableStatus) tableStatus.textContent = 'Synced Live';
            }
        })
        .catch(err => {
            console.error('Fetch submissions error:', err);
            const tableStatus = document.getElementById('table-sync-status');
            if (tableStatus) tableStatus.textContent = 'Sync Error';
        });
}

function populateFilterDropdowns() {
    const deptSelect = document.getElementById('filter-dept');
    if (!deptSelect) return;
    
    // Extract unique departments, keeping current selection
    const currentSelection = tableFilters.department;
    const depts = [...new Set(tableSubmissions.map(x => x.department))].sort();
    
    let html = '<option value="">All Departments</option>';
    depts.forEach(d => {
        html += `<option value="${escapeHTML(d)}" ${d === currentSelection ? 'selected' : ''}>${escapeHTML(d)}</option>`;
    });
    deptSelect.innerHTML = html;
}

function renderTable() {
    const tbody = document.getElementById('submissions-table-body');
    if (!tbody) return;
    
    // Filter list
    let filtered = tableSubmissions.filter(item => {
        const matchesSearch = item.student_name.toLowerCase().includes(tableFilters.search) || 
                              item.department.toLowerCase().includes(tableFilters.search);
                              
        const matchesDept = tableFilters.department === '' || item.department === tableFilters.department;
        const matchesSem = tableFilters.semester === '' || item.semester === tableFilters.semester;
        
        return matchesSearch && matchesDept && matchesSem;
    });
    
    // Sort list
    filtered.sort((a, b) => {
        let valA = a[tableFilters.sortBy];
        let valB = b[tableFilters.sortBy];
        
        if (typeof valA === 'string') {
            valA = valA.toLowerCase();
            valB = valB.toLowerCase();
        }
        
        if (valA < valB) return tableFilters.sortOrder === 'asc' ? -1 : 1;
        if (valA > valB) return tableFilters.sortOrder === 'asc' ? 1 : -1;
        return 0;
    });
    
    // Render list
    if (filtered.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="py-12 text-center text-slate-500 text-sm">
                    No submissions found matching the criteria.
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    filtered.forEach(sub => {
        const dateObj = new Date(sub.created_at);
        const dateStr = dateObj.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }) + ' ' + 
                        dateObj.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
        
        html += `
            <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                <td class="px-6 py-4.5">
                    <div class="font-semibold text-white">${escapeHTML(sub.student_name)}</div>
                    <div class="text-[10px] text-slate-500 font-medium">ID: #${sub.id}</div>
                </td>
                <td class="px-6 py-4.5 text-slate-300 text-sm font-medium">${escapeHTML(sub.department)}</td>
                <td class="px-6 py-4.5">
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/5 text-slate-300 border border-white/5">
                        ${escapeHTML(sub.semester)}
                    </span>
                </td>
                <td class="px-6 py-4.5 text-slate-400 text-xs">${dateStr}</td>
                <td class="px-6 py-4.5 text-right whitespace-nowrap">
                    <div class="flex items-center justify-end space-x-2">
                        <!-- View Action -->
                        <a href="../uploads/${sub.pdf_file}" target="_blank" class="w-8 h-8 rounded-lg flex items-center justify-center bg-emerald-600/10 hover:bg-emerald-600 text-emerald-400 hover:text-white border border-emerald-500/20 hover:border-emerald-500 transition-all duration-200" title="View Entry PDF">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.43 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </a>
                        <!-- Download Action -->
                        <a href="../uploads/${sub.pdf_file}" download="${escapeHTML(sub.student_name.replace(/\s+/g, '_'))}_Essay.pdf" class="w-8 h-8 rounded-lg flex items-center justify-center bg-teal-600/10 hover:bg-teal-600 text-teal-400 hover:text-white border border-teal-500/20 hover:border-teal-500 transition-all duration-200" title="Download PDF">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                        </a>
                        <!-- Delete Action -->
                        <button type="button" onclick="confirmDeleteSubmission(${sub.id}, '${escapeHTML(sub.student_name)}')" class="w-8 h-8 rounded-lg flex items-center justify-center bg-rose-600/10 hover:bg-rose-600 text-rose-400 hover:text-white border border-rose-500/20 hover:border-rose-500 transition-all duration-200" title="Delete Submission">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    tbody.innerHTML = html;
}

function handleSort(column) {
    if (tableFilters.sortBy === column) {
        // Toggle direction
        tableFilters.sortOrder = tableFilters.sortOrder === 'asc' ? 'desc' : 'asc';
    } else {
        tableFilters.sortBy = column;
        tableFilters.sortOrder = 'asc';
    }
    
    // Update sort headers markers
    const headers = document.querySelectorAll('th[data-sortable]');
    headers.forEach(h => {
        const indicator = h.querySelector('.sort-indicator');
        if (!indicator) return;
        
        if (h.getAttribute('data-col') === column) {
            indicator.textContent = tableFilters.sortOrder === 'asc' ? ' ↑' : ' ↓';
            h.classList.add('text-emerald-400');
        } else {
            indicator.textContent = '';
            h.classList.remove('text-emerald-400');
        }
    });
    
    renderTable();
}

// Confirmation Modals for Delete
let deleteIdTarget = null;
function confirmDeleteSubmission(id, name) {
    deleteIdTarget = id;
    const modal = document.getElementById('delete-confirm-modal');
    const nameSpan = document.getElementById('delete-target-name');
    
    if (modal && nameSpan) {
        nameSpan.textContent = name;
        modal.classList.remove('pointer-events-none', 'opacity-0');
        modal.querySelector('.relative').classList.remove('scale-95');
    }
}

function closeDeleteModal() {
    deleteIdTarget = null;
    const modal = document.getElementById('delete-confirm-modal');
    if (modal) {
        modal.classList.add('pointer-events-none', 'opacity-0');
        modal.querySelector('.relative').classList.add('scale-95');
    }
}

function executeDeleteSubmission() {
    if (deleteIdTarget === null) return;
    
    const csrfToken = document.getElementById('table-csrf-token')?.value || '';
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', deleteIdTarget);
    formData.append('csrf_token', csrfToken);
    
    fetch('../api/submissions.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            // Delete complete, fetch new table state
            fetchSubmissions();
            closeDeleteModal();
            showToastNotification('Submission deleted successfully');
        } else {
            alert(res.message || 'Failed to delete submission');
        }
    })
    .catch(err => {
        console.error('Deletion failure:', err);
        alert('Server connection failure. Could not delete submission.');
    });
}

// Helper: Escape strings for safe UI template interpolation
function escapeHTML(str) {
    if (str === null || str === undefined) return '';
    return str.replace(/[&<>'"]/g, 
        tag => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            "'": '&#39;',
            '"': '&quot;'
        }[tag] || tag)
    );
}
