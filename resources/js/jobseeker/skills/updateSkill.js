
   window.updateSkillsInput = function() {
            const skills = [];
            $('#skillsTags .badge').each(function() {
                const text = $(this).text().trim();
                if (text) {
                    skills.push(text);
                }
            });
            $('#skills_input').val(skills.join(', '));
        }
