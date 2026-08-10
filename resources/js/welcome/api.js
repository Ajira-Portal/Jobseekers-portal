function welcomeCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

window.welcomeApi = {
    getJobs(params) {
        return $.ajax({
            url: '/jobs',
            type: 'GET',
            data: params || {}
        });
    },

    registerGraduate(data) {
        return $.ajax({
            url: '/graduates/register',
            type: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': welcomeCsrfToken()
            }
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
