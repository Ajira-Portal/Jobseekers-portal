

window.loadEducation = function() {
            $.ajax({
                url: '/jobseeker/education',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        renderEducation(response.education);
                    }
                },
                error: function() {
                    console.error('Error loading education');
                }
            });
        }

        window.renderEducation = function(educations) {
            const container = $('#educationList');
            if (!educations || educations.length === 0) {
                container.html(`
                    <div class="empty-state">
                        <i class="fas fa-graduation-cap"></i>
                        <p>No education added yet. Click "Add Education" to get started.</p>
                    </div>
                `);
                return;
            }
            let html = '';
            educations.forEach(function(edu) {
                let details = '';
                let badgeColor = 'secondary';
                
                if (edu.type === 'informal') {
                    details = 'Informal Education: ' + (edu.description || '');
                    badgeColor = 'info';
                } else if (edu.type === 'primary') {
                    details = 'Primary: ' + (edu.school_name || '');
                    badgeColor = 'success';
                } else if (edu.type === 'secondary') {
                    details = 'Secondary: ' + (edu.school_name || '');
                    badgeColor = 'warning';
                } else if (edu.type === 'university') {
                    details = 'University: ' + (edu.institution || '') + ' - ' + (edu.course || '');
                    if (edu.level) {
                        details += ' (' + edu.level.charAt(0).toUpperCase() + edu.level.slice(1) + ')';
                    }
                    badgeColor = 'primary';
                }
                
                const dateFormatter = typeof window.formatDate === 'function'
                    ? window.formatDate
                    : function(dateString) {
                        if (!dateString) return 'N/A';
                        const date = new Date(dateString);
                        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short' });
                    };

                var dateRange = edu.start_date ? 
                    dateFormatter(edu.start_date) + ' - ' + (edu.end_date ? dateFormatter(edu.end_date) : 'Present') : 
                    'No dates specified';
                
                html += `
                    <div class="entry-card">
                        <div class="entry-info">
                            <h6>
                                <span class="badge bg-${badgeColor} me-2">${edu.type.charAt(0).toUpperCase() + edu.type.slice(1)}</span>
                                ${details}
                            </h6>
                            <small class="text-muted">
                                <i class="far fa-calendar-alt me-1"></i>
                                ${dateRange}
                            </small>
                        </div>
                        <div class="entry-actions">
                            <button type="button" class="btn-edit-sm" onclick="editEducation(${edu.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn-danger-sm" onclick="deleteEducation(${edu.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            container.html(html);
        }
