  // ============================================
        // VALIDATE INFO
        // ============================================
        function validateStep1() {
            let valid = true;
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            const residentNumber = $('#resident_number').val().trim();
            if (!residentNumber) {
                $('#resident_number').addClass('is-invalid');
                $('#residentNumberError').text('Please enter your resident number.');
                valid = false;
            }

            const name = $('#name').val().trim();
            if (!name) {
                $('#name').addClass('is-invalid');
                $('#nameError').text('Please enter your full name.');
                valid = false;
            }

            const phone = $('#phone').val().trim();
            if (!phone) {
                $('#phone').addClass('is-invalid');
                $('#phoneError').text('Please enter your phone number.');
                valid = false;
            }

            const region = $('#region').val();
            if (!region) {
                $('#region').addClass('is-invalid');
                $('#regionError').text('Please select a region.');
                valid = false;
            }

            const district = $('#district').val();
            if (!district) {
                $('#district').addClass('is-invalid');
                $('#districtError').text('Please select a district.');
                valid = false;
            }

            const ward = $('#ward').val();
            if (!ward) {
                $('#ward').addClass('is-invalid');
                $('#wardError').text('Please select a ward.');
                valid = false;
            }

            const shehia = $('#shehia').val();
            if (!shehia) {
                $('#shehia').addClass('is-invalid');
                $('#shehiaError').text('Please select a shehia.');
                valid = false;
            }

            return valid;
        }
