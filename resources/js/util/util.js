// This is what we created in the modules
// In init.js
export function init() {
    loadDashboardStats();
    loadRecentJobs();
    loadEducation();
    initSkillsDropdown();
    initLocationDropdowns();
    initEducationTypeToggle();

// In index.js - auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
}