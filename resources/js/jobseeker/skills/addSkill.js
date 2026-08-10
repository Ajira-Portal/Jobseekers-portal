// resources/js/skill/skill.js

/**
 * Skills Module - Handles skill selection, management, and AJAX saving
 */

class SkillManager {
    constructor() {
        this.selectedSkills = [];
        this.skillsInput = null;
        this.skillCount = null;
        this.dropdown = null;
        this.customArea = null;
        this.customInput = null;
        this.addCustomBtn = null;
        this.selectedContainer = null;
        
        this.init();
    }

    init() {
        // Get DOM elements
        this.skillsInput = document.getElementById('skillsInput');
        this.skillCount = document.getElementById('skillCount');
        this.dropdown = document.getElementById('skillsDropdown');
        this.customArea = document.getElementById('customSkillsArea');
        this.customInput = document.getElementById('customSkillsInput');
        this.addCustomBtn = document.getElementById('addCustomSkillsBtn');
        this.selectedContainer = document.getElementById('selectedSkills');
        
        if (!this.dropdown) {
            return;
        }
        
        // Initialize existing skills
        this.loadExistingSkills();
        
        // Setup event listeners
        this.setupEventListeners();
    }

    loadExistingSkills() {
        // Load skills from hidden input
        const existingSkills = this.skillsInput?.value || '';
        if (existingSkills) {
            const skills = existingSkills.split(',').map(s => s.trim()).filter(s => s);
            skills.forEach(skill => {
                this.selectedSkills.push(skill);
                this.renderSkillTag(skill, false);
            });
            this.updateSkillCount();
        }
        
        // Also load from any existing skill tags
        this.selectedContainer?.querySelectorAll('.skill-tag').forEach(tag => {
            const skill = tag.dataset.skill;
            if (skill && !this.selectedSkills.includes(skill)) {
                this.selectedSkills.push(skill);
            }
        });
        this.updateSkillCount();
    }

    setupEventListeners() {
        // Dropdown change - Add skill
        this.dropdown?.addEventListener('change', (e) => {
            const value = e.target.value;
            if (!value) return;

            if (value === '__other__') {
                this.showCustomInput();
                this.dropdown.value = '';
                return;
            }

            this.addSkill(value);
            this.dropdown.value = '';
        });

        // Custom skills - Add button
        this.addCustomBtn?.addEventListener('click', () => {
            this.addCustomSkills();
        });

        // Custom skills - Enter key
        this.customInput?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.addCustomSkills();
            }
        });

        // Keyboard shortcut: Enter in dropdown
        this.dropdown?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const value = this.dropdown.value;
                if (value && value !== '__other__') {
                    this.addSkill(value);
                    this.dropdown.value = '';
                }
            }
        });
    }

    showCustomInput() {
        if (this.customArea) {
            this.customArea.style.display = 'block';
            this.customInput?.focus();
        }
    }

    hideCustomInput() {
        if (this.customArea) {
            this.customArea.style.display = 'none';
            if (this.customInput) {
                this.customInput.value = '';
            }
        }
    }

    addCustomSkills() {
        const customSkills = this.customInput?.value?.trim() || '';
        if (!customSkills) {
            this.showToast('warning', 'Please enter at least one skill.');
            return;
        }

        const skills = customSkills.split(',').map(s => s.trim()).filter(s => s);
        
        if (skills.length === 0) {
            this.showToast('warning', 'Please enter valid skills.');
            return;
        }

        skills.forEach(skill => {
            this.addSkill(skill, true);
        });

        this.customInput.value = '';
        this.hideCustomInput();
        this.showToast('success', `${skills.length} custom skill(s) added!`);
    }

    addSkill(skill, isCustom = false) {
        skill = skill.trim();
        if (!skill) return;

        // Check for duplicates
        if (this.selectedSkills.includes(skill)) {
            this.showToast('warning', `"${skill}" is already added.`);
            return;
        }

        // Add to array
        this.selectedSkills.push(skill);
        
        // Render tag
        this.renderSkillTag(skill, isCustom);
        
        // Update UI
        this.updateSkillCount();
        this.updateHiddenInput();
        
        this.showToast('success', `"${skill}" added${isCustom ? ' as custom' : ''}.`);
    }

    renderSkillTag(skill, isCustom = false) {
        // Remove empty message if exists
        const emptyMsg = this.selectedContainer?.querySelector('.empty-message');
        if (emptyMsg) emptyMsg.remove();

        const tag = document.createElement('span');
        tag.className = `skill-tag${isCustom ? ' custom-skill' : ''}`;
        tag.dataset.skill = skill;
        tag.innerHTML = `
            ${skill}
            ${isCustom ? ' <small>(custom)</small>' : ''}
            <i class="fas fa-times remove-skill" onclick="window.removeSkill('${skill}')"></i>
        `;
        
        this.selectedContainer?.appendChild(tag);
    }

    updateSkillCount() {
        if (this.skillCount) {
            this.skillCount.textContent = this.selectedSkills.length;
            this.skillCount.style.display = this.selectedSkills.length > 0 ? 'inline' : 'none';
        }
    }

    updateHiddenInput() {
        if (this.skillsInput) {
            this.skillsInput.value = this.selectedSkills.join(', ');
        }
    }

    removeSkill(skill) {
        // Remove from array
        this.selectedSkills = this.selectedSkills.filter(s => s !== skill);
        
        // Remove from UI
        const tag = this.selectedContainer?.querySelector(`.skill-tag[data-skill="${skill}"]`);
        if (tag) tag.remove();
        
        // Show empty message if no skills
        if (this.selectedSkills.length === 0) {
            const emptyMsg = document.createElement('span');
            emptyMsg.className = 'empty-message';
            emptyMsg.textContent = 'No skills selected yet';
            this.selectedContainer?.appendChild(emptyMsg);
        }
        
        // Update UI
        this.updateSkillCount();
        this.updateHiddenInput();
        this.showToast('info', `"${skill}" removed.`);
    }

    // Toast Notification
    showToast(type, message) {
        const colors = {
            success: 'bg-success text-white',
            error: 'bg-danger text-white',
            info: 'bg-info text-white',
            warning: 'bg-warning text-dark'
        };

        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            info: 'fa-info-circle',
            warning: 'fa-exclamation-triangle'
        };

        const toastHtml = `
            <div class="toast align-items-center ${colors[type]} border-0 show" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas ${icons[type]} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999;';
            document.body.appendChild(container);
        }
        
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = toastHtml;
        const toast = tempDiv.firstElementChild;
        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 5000);

        toast.querySelector('.btn-close').addEventListener('click', function() {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        });
    }

    // Get all selected skills
    getSkills() {
        return this.selectedSkills;
    }

    // Get skills as comma-separated string
    getSkillsString() {
        return this.selectedSkills.join(', ');
    }

    // Reset all skills
    reset() {
        this.selectedSkills = [];
        if (this.selectedContainer) {
            this.selectedContainer.innerHTML = `
                <span class="empty-message">No skills selected yet</span>
            `;
        }
        this.updateSkillCount();
        this.updateHiddenInput();
        this.hideCustomInput();
    }
}

// ============================================
// EXPOSE TO WINDOW
// ============================================

let skillManager = null;

function shouldInitSkillManager() {
    return !!document.getElementById('skillsDropdown') && !!document.getElementById('selectedSkills');
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        if (!shouldInitSkillManager()) {
            return;
        }

        skillManager = new SkillManager();
        window.skillManager = skillManager;
    });
} else {
    if (shouldInitSkillManager()) {
        skillManager = new SkillManager();
        window.skillManager = skillManager;
    }
}

// Expose functions globally
window.addSkill = function(skill, isCustom = false) {
    if (skillManager) {
        skillManager.addSkill(skill, isCustom);
    }
};

window.removeSkill = function(skill) {
    if (skillManager) {
        skillManager.removeSkill(skill);
    }
};

window.getSkills = function() {
    return skillManager ? skillManager.getSkills() : [];
};

window.resetSkills = function() {
    if (skillManager) {
        skillManager.reset();
    }
};

// Export for module usage
export default SkillManager;