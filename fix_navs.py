import os
import glob

# Defines the navigation snippet for each role
# We use REPLACE_PREFIX to put the correct relative path (either ../ or ../../ depending on depth)
navs = {
    'admin': """
            <a href="{prefix}Supervisor/admin-supervisors.php" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Supervisors</span>
            </a>""",
    'staff': """
            <a href="{prefix}Supervisor/staff-supervision.php" class="nav-item">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Supervision</span>
            </a>""",
    'student': """
            <a href="{prefix}Supervisor/student-supervisor.php" class="nav-item">
                <i class="fas fa-user-tie"></i>
                <span>Supervisor</span>
            </a>""",
    'host-org': """
            <a href="{prefix}Supervisor/host-org-supervision.php" class="nav-item">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Supervision</span>
            </a>"""
}

# The folders to look in
search_dirs = [
    'Applications', 'Assessment', 'Dashboards', 'Logbook', 
    'Opportunities', 'Reports', 'Settings', 'Students', 'Supervisor'
]

def process_file(filepath):
    with open(filepath, 'r') as f:
        content = f.read()

    # Determine role
    filename = os.path.basename(filepath)
    role = None
    if 'admin' in filename: role = 'admin'
    elif 'staff' in filename: role = 'staff'
    elif 'student' in filename: role = 'student'
    elif 'host-org' in filename or 'host-management' in filename: role = 'host-org'

    if not role:
        return # Cannot determine role

    # Determine path prefix
    depth = len(filepath.split('/')) - 1
    # Base dir is Student-Placement-Attachment-System
    # So if it's in a subdirectory like 'Students/admin-students.php', depth=2 relative to base
    # Wait, simple way: count slashes in relative path
    rel_path = filepath.replace('./', '')
    parts = rel_path.split('/')
    if len(parts) == 1:
        prefix = ""
    elif len(parts) == 2:
        prefix = "../"
    elif len(parts) == 3:
        prefix = "../../"
    else:
        prefix = "../" * (len(parts) - 1)

    nav_snippet = navs[role].replace("{prefix}", prefix)
    
    # Check if the snippet essentially already exists
    if "Supervisor/" in content and "nav-item" in content:
        # Check if it has a supervisor link in the nav
        # Let's just do a specific check:
        if ('href="' + prefix + 'Supervisor/admin-supervisors.php"' in content or 
            'href="' + prefix + 'Supervisor/staff-supervision.php"' in content or
            'href="' + prefix + 'Supervisor/student-supervisor.php"' in content or
            'href="' + prefix + 'Supervisor/host-org-supervision.php"' in content or
            'href="../../Supervisor/' in content or
            'href="../Supervisor/' in content):
            # Probably already has it
            
            # Wait, there might be other links to Supervisor/ that aren't the nav.
            # But let's assume if it exists, it's there. Just verify.
            # Let's check more carefully:
            # We want to inject it before </nav> if "Supervisor" isn't in between <nav class="sidebar-nav"> and </nav>
            pass
            
            
    # Careful injection
    import re
    nav_match = re.search(r'(<nav class="sidebar-nav">.*?</nav>)', content, re.DOTALL)
    if nav_match:
        nav_block = nav_match.group(1)
        if 'Supervisor/' not in nav_block:
            # Inject before </nav>
            new_nav_block = nav_block.replace('</nav>', nav_snippet + '\n        </nav>')
            new_content = content.replace(nav_block, new_nav_block)
            with open(filepath, 'w') as f:
                f.write(new_content)
            print(f"Updated {filepath}")
        else:
            print(f"Skipped {filepath} (Already has Supervisor link)")

for d in search_dirs:
    for root, dirs, files in os.walk('./' + d):
        for file in files:
            if file.endswith('.php'):
                process_file(os.path.join(root, file))

