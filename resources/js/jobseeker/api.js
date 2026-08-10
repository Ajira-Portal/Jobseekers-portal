function csrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

function jsonHeaders() {
    return {
        'X-CSRF-TOKEN': csrfToken(),
        'Accept': 'application/json'
    };
}

window.jobseekerApi = {
    getDistricts(regionId) {
        return $.ajax({
            url: '/jobseeker/get-districts',
            type: 'GET',
            data: { region_id: regionId }
        });
    },

    getWards(districtId) {
        return $.ajax({
            url: '/jobseeker/get-wards',
            type: 'GET',
            data: { district_id: districtId }
        });
    },

    getShehias(wardId) {
        return $.ajax({
            url: '/jobseeker/get-shehias',
            type: 'GET',
            data: { ward_id: wardId }
        });
    },

    saveSkills(skills) {
        return $.ajax({
            url: '/jobseeker/skills/save',
            type: 'POST',
            data: { skills: skills },
            headers: jsonHeaders()
        });
    },

    updateProfile(formData) {
        return $.ajax({
            url: '/jobseeker/profile/update',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: jsonHeaders()
        });
    },

    getApplications() {
        return $.ajax({
            url: '/jobseeker/applications',
            type: 'GET'
        });
    },

    getJobs(params) {
        return $.ajax({
            url: '/jobseeker/jobs',
            type: 'GET',
            data: params || {}
        });
    },

    getJobDetails(id) {
        return $.ajax({
            url: `/jobseeker/jobs/${id}`,
            type: 'GET'
        });
    },

    getProfileStatus() {
        return $.ajax({
            url: '/jobseeker/profile/status',
            type: 'GET'
        });
    },

    applyForJob(jobId, formData) {
        return $.ajax({
            url: `/jobseeker/jobs/${jobId}/apply`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrfToken()
            }
        });
    },

    withdrawApplication(applicationId) {
        return $.ajax({
            url: `/jobseeker/applications/${applicationId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken()
            }
        });
    },

    getEducation() {
        return $.ajax({
            url: '/jobseeker/education',
            type: 'GET'
        });
    },

    completeProfile(formData) {
        return $.ajax({
            url: '/jobseeker/profile/complete',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrfToken()
            }
        });
    }
};
