function employerCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

window.employerApi = {
    getJobs() {
        return $.ajax({
            url: '/employer/jobs',
            type: 'GET'
        });
    },

    saveJob(url, formData) {
        return $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': employerCsrfToken()
            }
        });
    },

    getJob(id) {
        return $.ajax({
            url: `/employer/jobs/${id}`,
            type: 'GET'
        });
    },

    getApplications(jobId) {
        return $.ajax({
            url: `/employer/jobs/${jobId}/applications`,
            type: 'GET'
        });
    },

    getApplicantProfile(applicationId) {
        return $.ajax({
            url: `/employer/applications/${applicationId}/profile`,
            type: 'GET'
        });
    },

    updateApplicationStatus(applicationId, status) {
        return $.ajax({
            url: `/employer/applications/${applicationId}/status`,
            type: 'POST',
            data: {
                _method: 'PATCH',
                status: status
            },
            headers: {
                'X-CSRF-TOKEN': employerCsrfToken()
            }
        });
    },

    toggleJobStatus(id) {
        return $.ajax({
            url: `/employer/jobs/${id}/toggle-status`,
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': employerCsrfToken()
            }
        });
    },

    deleteJob(id) {
        return $.ajax({
            url: `/employer/jobs/${id}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': employerCsrfToken()
            }
        });
    }
};
