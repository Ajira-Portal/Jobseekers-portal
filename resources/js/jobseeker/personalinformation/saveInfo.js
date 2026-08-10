function saveStep1(callback) {
    const done = typeof callback === 'function' ? callback : function() {};

    if (!validateStep1()) {
        done(false);
        return;
    }

    // Get current values
    const formData = new FormData();
    formData.append('resident_number', $('#resident_number').val().trim());
    formData.append('name', $('#name').val().trim());
    formData.append('phone', $('#phone').val().trim());
    formData.append('gender', $('#gender').val());
    formData.append('date_of_birth', $('#date_of_birth').val());
    formData.append('disability', $('#disability').val());
    formData.append('disability_description', $('#disability_description').val().trim());
    formData.append('region_id', $('#region').val());
    formData.append('district_id', $('#district').val());
    formData.append('ward_id', $('#ward').val());
    formData.append('shehia_id', $('#shehia').val());

    const resumeFile = $('#resume')[0].files[0];
    const profileImageFile = $('#profile_image')[0].files[0];

    if (resumeFile) {
        formData.append('resume', resumeFile);
    }
    if (profileImageFile) {
        formData.append('profile_image', profileImageFile);
    }

    const saveBtn = $('button[onclick*="nextStep(2)"]');
    const originalText = saveBtn.html();
    saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Saving...');

    $.ajax({
        url: '/jobseeker/profile/update',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        },
        success: function(response) {
            saveBtn.prop('disabled', false).html(originalText);
            if (response.success) {
                showToast('success', response.message || 'General information saved.');
                // Store the saved data
                window.step1Data = {
                    resident_number: $('#resident_number').val(),
                    name: $('#name').val(),
                    phone: $('#phone').val(),
                    gender: $('#gender').val(),
                    date_of_birth: $('#date_of_birth').val(),
                    disability: $('#disability').val(),
                    disability_description: $('#disability_description').val(),
                    region_id: $('#region').val(),
                    district_id: $('#district').val(),
                    ward_id: $('#ward').val(),
                    shehia_id: $('#shehia').val()
                };
                done(true);
            } else {
                showToast('error', response.message || 'Unable to save general information.');
                done(false);
            }
        },
        error: function(xhr) {
            saveBtn.prop('disabled', false).html(originalText);

            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = xhr.responseJSON.errors;
                const fieldMap = {
                    region_id: 'region',
                    district_id: 'district',
                    ward_id: 'ward',
                    shehia_id: 'shehia'
                };

                Object.keys(errors).forEach(function(key) {
                    const fieldId = fieldMap[key] || key;
                    const field = $('#' + fieldId);
                    if (field.length) {
                        field.addClass('is-invalid');
                        $('#' + fieldId + 'Error').text(errors[key][0]);
                    }
                });
                showToast('error', 'Please fix the validation errors.');
            } else {
                showToast('error', xhr.responseJSON?.message || 'Unable to save general information.');
            }

            done(false);
        }
    });
}