   window.removeSkill = function(skill) {
            $('#skillsTags .badge').each(function() {
                if ($(this).text().trim() === skill) {
                    $(this).remove();
                    return false;
                }
            });
            updateSkillsInput();
        };