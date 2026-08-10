   window.deleteEducation = function(id) {
            if (!confirm('Are you sure you want to delete this education entry?')) return;
            $.ajax({
                url: `/jobseeker/education/${id}`,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(response) {
                    if (response.success) {
                        if (typeof window.showToast === 'function') {
                            window.showToast('success', response.message);
                        }
                        if (typeof window.loadEducation === 'function') {
                            window.loadEducation();
                        }
                    }
                },
                error: function() {
                    if (typeof window.showToast === 'function') {
                        window.showToast('error', 'Error deleting education.');
                    }
                }
            });
        };