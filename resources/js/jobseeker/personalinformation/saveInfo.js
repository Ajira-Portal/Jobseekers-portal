function saveStep1(callback) {
    const done = typeof callback === 'function' ? callback : function() {};

    if (!validateStep1()) {
        done(false);
        return;
    }

    const currentValues = {
        resident_number: ($('#resident_number').val() || '').trim(),
        name: ($('#name').val() || '').trim(),
        phone: ($('#phone').val() || '').trim(),
        gender: $('#gender').val() || '',
        date_of_birth: $('#date_of_birth').val() || '',
        disability: $('#disability').val() || 'no',
        disability_description: ($('#disability_description').val() || '').trim(),
        region_id: $('#region').val() || '',
        district_id: $('#district').val() || '',
        ward_id: $('#ward').val() || '',
        shehia_id: $('#shehia').val() || ''
    };

    const resumeFile = $('#resume')[0] ? $('#resume')[0].files[0] : null;
    const profileImageFile = $('#profile_image')[0] ? $('#profile_image')[0].files[0] : null;

    // Check if step 1 data was already saved and hasn't changed, without new file attachments
    if (window.step1Saved && window.step1Data && !resumeFile && !profileImageFile) {
        const hasChanged = Object.keys(currentValues).some(key => String(currentValues[key]) !== String(window.step1Data[key] || ''));
        if (!hasChanged) {
            done(true);
            return;
        }
    }

    // Get current values
    const formData = new FormData();
    Object.keys(currentValues).forEach(key => {
        formData.append(key, currentValues[key]);
    });

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
                window.step1Saved = true;
                window.step1Data = currentValues;
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