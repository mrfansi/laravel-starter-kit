import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Custom Admin JS functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize sidebar toggle for mobile
    const sidebarToggle = document.getElementById('sidebar-toggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('hidden');
        });
    }
    
    // Initialize dark mode toggle if present
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
            
            // Save preference to localStorage
            if (document.documentElement.classList.contains('dark')) {
                localStorage.setItem('darkMode', 'true');
            } else {
                localStorage.setItem('darkMode', 'false');
            }
        });
    }
    
    // Check for dark mode preference
    if (localStorage.getItem('darkMode') === 'true' || 
        (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    }
});
