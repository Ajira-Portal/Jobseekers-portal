 window.editEducation = function(id) {
            $.ajax({
                url: `/jobseeker/education/${id}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const edu = response.education;
                        $('#edu_id').val(edu.id);
                        $('#edu_type').val(edu.type);
                        $('#edu_type').trigger('change');
                        
                        $('#edu_informal_description').val('');
                        $('#edu_school_name').val('');
                        $('#edu_institution').val('');
                        $('#edu_course').val('');
                        $('#edu_level').val('');
                        $('#edu_start_date').val('');
                        $('#edu_end_date').val('');
                        $('#edu_is_current').prop('checked', false);
                        $('#edu_end_date').prop('disabled', false);
                        
                        if (edu.type === 'informal') {
                            $('#edu_informal_description').val(edu.description || '');
                        } else if (edu.type === 'primary' || edu.type === 'secondary') {
                            $('#edu_school_name').val(edu.school_name || '');
                            $('#edu_start_date').val(edu.start_date || '');
                            $('#edu_end_date').val(edu.end_date || '');
                        } else if (edu.type === 'university') {
                            $('#edu_institution').val(edu.institution || '');
                            $('#edu_course').val(edu.course || '');
                            $('#edu_level').val(edu.level || '');
                            $('#edu_university_start_date').val(edu.start_date || '');
                            $('#edu_university_end_date').val(edu.end_date || '');
                        }
                        
                        $('#educationFormContainer').show();
                        if (typeof window.clearValidation === 'function') {
                            window.clearValidation('edu_');
                        }
                        $('html, body').animate({
                            scrollTop: $('#educationFormContainer').offset().top - 100
                        }, 500);
                    }
                },
                error: function() {
                    if (typeof window.showToast === 'function') {
                        window.showToast('error', 'Error loading education details.');
                    }
                }
            });
        };