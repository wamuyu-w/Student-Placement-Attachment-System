function handleViewDetails(btn) {
    const orgName = btn.getAttribute('data-org');
    const description = btn.getAttribute('data-desc');
    const criteria = btn.getAttribute('data-crit');
    const deadline = btn.getAttribute('data-deadline');
    const opportunityId = btn.getAttribute('data-id');
    openDetailsModal(orgName, description, criteria, deadline, opportunityId);
}

function handleApplyForm(btn) {
    const opportunityId = btn.getAttribute('data-id');
    const orgName = btn.getAttribute('data-org');
    const description = btn.getAttribute('data-desc');
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
    
    alertDiv.className = 'alert ' + type;
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
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    
    const formData = new FormData(this);
    const basePath = window.location.pathname.substring(0, window.location.pathname.indexOf('/public/'));
    
    fetch(basePath + '/public/student/opportunities/apply', {
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
            showAlert(data.message, 'error');
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

// Drag and drop for file upload
const fileUpload = document.getElementById('resume');
const uploadArea = fileUpload ? fileUpload.parentElement.querySelector('.upload-area') : null;

if (uploadArea) {
    uploadArea.addEventListener('click', () => fileUpload.click());

    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.style.backgroundColor = '#f0f0f0';
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.style.backgroundColor = '';
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        if (e.dataTransfer.files.length) {
            fileUpload.files = e.dataTransfer.files;
            updateFileName(e.dataTransfer.files[0].name);
        }
        uploadArea.style.backgroundColor = '';
    });
}

if (fileUpload) {
    fileUpload.addEventListener('change', (e) => {
        if (fileUpload.files.length) {
            updateFileName(fileUpload.files[0].name);
        }
    });
}

function updateFileName(name) {
    if (!uploadArea) return;
    const hint = uploadArea.querySelector('.upload-hint');
    if (hint) {
        hint.textContent = 'Selected: ' + name;
        hint.style.color = '#10b981';
        hint.style.fontWeight = '600';
    }
}

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
