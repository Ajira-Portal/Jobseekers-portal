 window.showEducationForm = function() {
            $('#educationFormContainer').show();
            $('#edu_id').val('');
            $('#edu_type').val('');
            $('#edu_informal_description').val('');
            $('#edu_school_name').val('');
            $('#edu_institution').val('');
            $('#edu_course').val('');
            $('#edu_level').val('');
            $('#edu_start_date').val('');
            $('#edu_end_date').val('');
            $('#edu_is_current').prop('checked', false);
            $('#edu_end_date').prop('disabled', false);
            $('.edu-conditional-fields').removeClass('show');
            if (typeof window.clearValidation === 'function') {
                window.clearValidation('edu_');
            }
            $('html, body').animate({
                scrollTop: $('#educationFormContainer').offset().top - 100
            }, 500);
        };

window.hideEducationForm = function() {
            $('#educationFormContainer').hide();
            if (typeof window.clearValidation === 'function') {
                window.clearValidation('edu_');
            }
        };