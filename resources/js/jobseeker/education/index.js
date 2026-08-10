
// Import all education functions from individual files
import { saveEducation } from './saveEducation.js';
import { editEducation } from './editEducation.js';
import { deleteEducation } from './deleteEducation.js';
import {loadRenderEducation } from './loadRenderEducation.js';
import { showEducation } from './showEducation.js';



// Export all functions so they can be imported elsewhere
export {
    saveEducation,
    editEducation,
    deleteEducation,
    loadRenderEducation,
    showEducation
};