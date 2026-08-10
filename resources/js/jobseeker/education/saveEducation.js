 window.saveEducation = function() {
            const id = $('#edu_id').val();
            const type = $('#edu_type').val();
            
            let data = { type: type };
            let valid = true;

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            if (!type) {
                $('#edu_type').addClass('is-invalid');
                $('#edu_typeError').text('Please select education type.');
                valid = false;
            }

            if (type === 'informal') {
                const description = $('#edu_informal_description').val().trim();
                if (!description) {
                    $('#edu_informal_description').addClass('is-invalid');
                    $('#edu_informal_descriptionError').text('Description is required.');
                    valid = false;
                }
                data.description = description || '';
            } else if (type === 'primary' || type === 'secondary') {
                let schoolName = $('#edu_school_name').val().trim();
                let startDate = $('#edu_start_date').val();
                let endDate = $('#edu_end_date').val();
                
                if (!schoolName) {
                    $('#edu_school_name').addClass('is-invalid');
                    $('#edu_school_nameError').text('School name is required.');
                    valid = false;
                }
                if (!startDate) {
                    $('#edu_start_date').addClass('is-invalid');
                    $('#edu_start_dateError').text('Start date is required.');
                    valid = false;
                }
                
                data.school_name = schoolName || '';
                data.start_date = startDate || '';
                data.end_date = endDate || '';
            } else if (type === 'university') {
                let institution = $('#edu_institution').val().trim();
                let course = $('#edu_course').val().trim();
                let level = $('#edu_level').val();
                let startDate = $('#edu_university_start_date').val();
                let endDate = $('#edu_university_end_date').val();
                
                if (!institution) {
                    $('#edu_institution').addClass('is-invalid');
                    $('#edu_institutionError').text('Institution is required.');
                    valid = false;
                }
                if (!course) {
                    $('#edu_course').addClass('is-invalid');
                    $('#edu_courseError').text('Course is required.');
                    valid = false;
                }
                if (!level) {
                    $('#edu_level').addClass('is-invalid');
                    $('#edu_levelError').text('Level is required.');
                    valid = false;
                }
                if (!startDate) {
                    $('#edu_university_start_date').addClass('is-invalid');
                    $('#edu_university_start_dateError').text('Start date is required.');
                    valid = false;
                }
                
                data.institution = institution || '';
                data.course = course || '';
                data.level = level || '';
                data.start_date = startDate || '';
                data.end_date = endDate || '';
            }

            if (!valid) {
                return;
            }

            Object.keys(data).forEach(function(key) {
                if (data[key] === '' || data[key] === null || data[key] === undefined) {
                    delete data[key];
                }
            });

            const url = id ? `/jobseeker/education/${id}` : '/jobseeker/education';
            const method = id ? 'PUT' : 'POST';

            const saveBtn = $('button[onclick="saveEducation()"]');
            const originalText = saveBtn.html();
            saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Saving...');

            $.ajax({
                url: url,
                type: 'POST',
                data: { ...data, _method: method },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.success) {
                        if (typeof window.showToast === 'function') {
                            window.showToast('success', response.message);
                        }
                        if (typeof window.loadEducation === 'function') {
                            window.loadEducation();
                        }
                        if (typeof window.hideEducationForm === 'function') {
                            window.hideEducationForm();
                        }
                    } else {
                        if (typeof window.showToast === 'function') {
                            window.showToast('error', response.message || 'Error saving education.');
                        }
                    }
                    saveBtn.prop('disabled', false).html(originalText);
                },
                error: function(xhr) {
                    saveBtn.prop('disabled', false).html(originalText);
                    
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(function(key) {
                            const field = $(`#edu_${key}`);
                            if (field.length) {
                                field.addClass('is-invalid');
                                $(`#edu_${key}Error`).text(errors[key][0]);
                            }
                        });
                        if (typeof window.showToast === 'function') {
                            window.showToast('error', 'Please fix the validation errors.');
                        }
                        return;
                    }

                    if (typeof window.showToast === 'function') {
                        window.showToast('error', 'Error saving education.');
                    }
                }
            });
        };