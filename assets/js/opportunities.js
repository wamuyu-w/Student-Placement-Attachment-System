function handleViewDetails(btn) {
    //getting all the necessary attributes and constants for the functions
    const orgName = btn.getAttribute('data-org');
    const description = btn.getAttribute('data-desc');
    const criteria = btn.getAttribute('data-crit');
    const deadline = btn.getAttribute('data-deadline');
    
    //call OpenDetails Modal
    const opportunityId = btn.getAttribute('data-id');
    openDetailsModal(orgName, description, criteria, deadline, opportunityId);
}

function handleApplyForm(btn) {
    //handles the application form
    const opportunityId = btn.getAttribute('data-id');
    const orgName = btn.getAttribute('data-org');
    const description = btn.getAttribute('data-desc');

    //function called here
    openApplicationForm(opportunityId, orgName, description);
}

function openDetailsModal(orgName, description, criteria, deadline, opportunityId) {
    document.getElementById('detailsOrg').textContent = orgName;
    document.getElementById('detailsRole').textContent = description; 
    document.getElementById('detailsDesc').textContent = description; 
    document.getElementById('detailsCriteria').textContent = criteria;
    document.getElementById('detailsDeadline').textContent = deadline;
    
    const applyBtn = document.getElementById('detailsApplyBtn');
    applyBtn.onclick = function() {
        closeDetailsModal();
        openApplicationForm(opportunityId, orgName, description);
    };
    
    document.getElementById('detailsModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeDetailsModal() {
    document.getElementById('detailsModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function openApplicationForm(opportunityId, orgName, description) {
    document.getElementById('opportunityId').value = opportunityId;
    document.getElementById('modalOrg').textContent = orgName;
    document.getElementById('modalRole').textContent = description;
    
    document.getElementById('applicationForm').reset();
    document.getElementById('charCount').textContent = '0';
    document.getElementById('formAlert').style.display = 'none';
    document.getElementById('applicationModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeApplicationForm() {
    document.getElementById('applicationModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function showAlert(message, type = 'success') {
    const alertDiv = document.getElementById('formAlert');
    const alertMessage = document.getElementById('alertMessage');

    alertDiv.className = 'alert' + type;
    alertMessage.textContent = message;
    alertDiv.style.display = 'flex';
}

// Character count for motivation
const motivationInput = document.getElementById('motivation');
if (motivationInput) {
    motivationInput.addEventListener('input', function() {
        document.getElementById('charCount').textContent = this.value.length;
    });
}

// Form submission
const applicationForm = document.getElementById('applicationForm');
if (applicationForm) {
    applicationForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting';
    
    const formData = new FormData(this);
    const url = getRouteUrl('student/opportunities/apply');
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            setTimeout(() => {
                closeApplicationForm();
                location.reload();
            }, 500);
        } else {
            // this is where the issue lies
            if (window.showErrorPopup) {
                window.showErrorPopup(data.message);
            } else {
                showAlert(data.message, 'error');
            }
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred. Please try again.', 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
    });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const appModal = document.getElementById('applicationModal');
    const detailsModal = document.getElementById('detailsModal');
    if (event.target === appModal) {
        closeApplicationForm();
    }
    if (event.target === detailsModal) {
        closeDetailsModal();
    }
};


// Search functionality
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('keyup', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.opportunity-card');
        
        cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            card.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
}
