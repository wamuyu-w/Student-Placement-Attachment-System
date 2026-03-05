<?php use App\Core\Helpers; ?>


    <div class="bg-white p-6 rounded-lg shadow-sm" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin: 0;">Student Assessment Form</h2>
            <a href="<?= Helpers::baseUrl('/staff/supervision') ?>" style="color: #6b7280; text-decoration: none;"><i class="fas fa-times"></i> Cancel</a>
        </div>

        <form action="<?= Helpers::baseUrl('/assessment/submit') ?>" method="POST" id="assessmentForm">
            <input type="hidden" name="attachment_id" value="<?= $attachment_id ?>">
            
            <div class="criteria-section">
                <h3 style="font-size: 1.1rem; color: #8B1538; margin-bottom: 15px; border-left: 4px solid #8B1538; padding-left: 10px;">Performance Criteria (Score 1-10)</h3>
                
                <?php 
                $criteriaList = [
                    "Availability of required documents",
                    "Degree of Organization of Daily Entries in the Logbook",
                    "Ability to work in teams",
                    "Accomplishment of Assignments",
                    "Presence at designated areas",
                    "Communication Skills",
                    "Mannerisms",
                    "Level of adaptability of the attachee in the organization",
                    "Student's understanding of assignments/tasks given",
                    "Oral Presentation"
                ];
                
                foreach ($criteriaList as $index => $criteria): 
                ?>
                <div class="form-group" style="margin-bottom: 15px; display: grid; grid-template-columns: 1fr 100px; gap: 20px; align-items: center;">
                    <label style="font-weight: 500; color: #374151;"><?= ($index + 1) . ". " . $criteria ?></label>
                    <input type="number" name="criteria[]" class="criteria-score form-control" min="0" max="10" required style="padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                </div>
                <?php endforeach; ?>
            </div>

            <div class="summary-section" style="background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 1.2rem; font-weight: 700;">
                    <span>Total Score:</span>
                    <span id="totalDisplay" style="color: #8B1538;">0 / 100</span>
                    <input type="hidden" name="total_score" id="totalScoreInput">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">General Remarks / Recommendations</label>
                <textarea name="comments" rows="4" class="form-control" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 4px;"></textarea>
            </div>

            <div style="text-align: right;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 30px; font-size: 1rem; background-color: #8B1538; color: white; border: none; border-radius: 6px; cursor: pointer;">
                    Submit Assessment
                </button>
            </div>
        </form>
    </div>


<script>
    // Auto-calculate total
    const inputs = document.querySelectorAll('.criteria-score');
    const totalDisplay = document.getElementById('totalDisplay');
    const totalInput = document.getElementById('totalScoreInput');

    function calculateTotal() {
        let total = 0;
        inputs.forEach(input => {
            const val = parseInt(input.value) || 0;
            total += val;
        });
        totalDisplay.textContent = total + " / 100";
        totalInput.value = total;
    }

    inputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
    });
</script>
