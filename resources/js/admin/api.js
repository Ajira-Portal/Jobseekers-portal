function adminCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

window.adminApi = {
    getStats() {
        return $.ajax({ url: '/admin/stats', type: 'GET' });
    },

    getGraduateStats() {
        return $.ajax({ url: '/admin/graduates/stats', type: 'GET' });
    },

    getPendingJobs() {
        return $.ajax({ url: '/admin/pending-jobs', type: 'GET' });
    },

    getAllJobs(params) {
        return $.ajax({ url: '/admin/all-jobs', type: 'GET', data: params || {} });
    },

    getEmployers() {
        return $.ajax({ url: '/admin/employers', type: 'GET' });
    },

    getJobseekers() {
        return $.ajax({ url: '/admin/jobseekers', type: 'GET' });
    },

    getGraduates(params) {
        return $.ajax({ url: '/admin/graduates', type: 'GET', data: params || {} });
    },

    saveGraduate(url, formData) {
        return $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': adminCsrfToken() }
        });
    },

    getGraduate(id) {
        return $.ajax({ url: `/admin/graduates/${id}`, type: 'GET' });
    },

    deleteGraduate(id) {
        return $.ajax({
            url: `/admin/graduates/${id}`,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': adminCsrfToken() }
        });
    },

    importGraduates(formData, xhrBuilder) {
        return $.ajax({
            url: '/admin/graduates/import',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': adminCsrfToken() },
            xhr: xhrBuilder
        });
    },

    exportGraduateTemplate() {
        return $.ajax({ url: '/admin/graduates/export/template', type: 'GET' });
    },

    getUsers(params) {
        return $.ajax({ url: '/admin/users', type: 'GET', data: params || {} });
    },

    saveUser(url, formData) {
        return $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': adminCsrfToken() }
        });
    },

    getUser(id) {
        return $.ajax({ url: `/admin/users/${id}`, type: 'GET' });
    },

    toggleUserStatus(id) {
        return $.ajax({
            url: `/admin/users/${id}/toggle-status`,
            type: 'PATCH',
            headers: { 'X-CSRF-TOKEN': adminCsrfToken() }
        });
    },

    deleteUser(id) {
        return $.ajax({
            url: `/admin/users/${id}`,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': adminCsrfToken() }
        });
    },

    approveJob(id) {
        return $.ajax({
            url: `/admin/jobs/${id}/approve`,
            type: 'POST',
            data: { _method: 'PATCH' },
            headers: { 'X-CSRF-TOKEN': adminCsrfToken() }
        });
    },

    rejectJob(id, reason) {
        return $.ajax({
            url: `/admin/jobs/${id}/reject`,
            type: 'POST',
            data: {
                _method: 'PATCH',
                rejection_reason: reason
            },
            headers: { 'X-CSRF-TOKEN': adminCsrfToken() }
        });
    },

    deleteJob(id) {
        return $.ajax({
            url: `/admin/jobs/${id}`,
            type: 'DELETE',
            headers: { 'X-CSRF-TOKEN': adminCsrfToken() }
        });
    },

    getJob(id) {
        return $.ajax({ url: `/admin/jobs/${id}`, type: 'GET' });
    },

    generateReport(data) {
        return $.ajax({
            url: '/admin/reports/generate',
            type: 'POST',
            data: data,
            headers: { 'X-CSRF-TOKEN': adminCsrfToken() }
        });
    },

    getRegions() {
        return $.get('/locations/regions');
    },

    getDistricts(regionId) {
        return $.get('/locations/districts', { region_id: regionId });
    },

    getWards(districtId) {
        return $.get('/locations/wards', { district_id: districtId });
    },

    getShehias(wardId) {
        return $.get('/locations/shehias', { ward_id: wardId });
    }
};
