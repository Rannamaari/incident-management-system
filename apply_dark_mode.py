#!/usr/bin/env python3
"""
Apply dark mode patterns to Laravel Blade form files
Based on DARK_MODE_FORM_PATTERNS.md
"""

import re
import sys
from pathlib import Path

def apply_dark_mode_patterns(content):
    """Apply dark mode class patterns to file content"""

    # Track if any changes were made
    original = content

    # Pattern replacements - only add if not already present
    replacements = [
        # Borders
        (r'border-gray-300(?!\s+dark:border-)', r'border-gray-300 dark:border-gray-600'),
        (r'border-gray-200(?!\s+dark:border-)', r'border-gray-200 dark:border-gray-700'),
        (r'border-gray-100(?!\s+dark:border-)', r'border-gray-100 dark:border-gray-700'),
        (r'border-red-300(?!\s+dark:border-)', r'border-red-300 dark:border-red-700'),
        (r'border-green-200(?!\s+dark:border-)', r'border-green-200 dark:border-green-700'),
        (r'border-green-300(?!\s+dark:border-)', r'border-green-300 dark:border-green-700'),
        (r'border-purple-200(?!\s+dark:border-)', r'border-purple-200 dark:border-purple-700'),
        (r'border-purple-300(?!\s+dark:border-)', r'border-purple-300 dark:border-purple-700'),
        (r'border-blue-200(?!\s+dark:border-)', r'border-blue-200 dark:border-blue-700'),

        # Backgrounds
        (r'bg-white(?!\s+dark:bg-)(?!\/)' , r'bg-white dark:bg-gray-800'),
        (r'bg-white/80(?!\s+dark:bg-)', r'bg-white/80 dark:bg-gray-800/80'),
        (r'bg-gray-100(?!\s+dark:bg-)', r'bg-gray-100 dark:bg-gray-900'),

        # Text colors
        (r'text-gray-900(?!\s+dark:text-)', r'text-gray-900 dark:text-gray-100'),
        (r'text-gray-700(?!\s+dark:text-)', r'text-gray-700 dark:text-gray-300'),
        (r'text-gray-600(?!\s+dark:text-)', r'text-gray-600 dark:text-gray-400'),
        (r'text-gray-500(?!\s+dark:text-)', r'text-gray-500 dark:text-gray-400'),
        (r'text-red-600(?!\s+dark:text-)', r'text-red-600 dark:text-red-400'),
        (r'text-green-700(?!\s+dark:text-)', r'text-green-700 dark:text-green-400'),
        (r'text-purple-700(?!\s+dark:text-)', r'text-purple-700 dark:text-purple-400'),
        (r'text-blue-700(?!\s+dark:text-)', r'text-blue-700 dark:text-blue-400'),

        # Placeholder
        (r'placeholder-gray-400(?!\s+dark:placeholder-)', r'placeholder-gray-400 dark:placeholder-gray-500'),

        # Focus borders
        (r'focus:border-blue-600(?!\s+dark:focus:border-)', r'focus:border-blue-600 dark:focus:border-blue-400'),
        (r'focus:border-green-600(?!\s+dark:focus:border-)', r'focus:border-green-600 dark:focus:border-green-400'),
        (r'focus:border-purple-600(?!\s+dark:focus:border-)', r'focus:border-purple-600 dark:focus:border-purple-400'),

        # Focus rings
        (r'focus:ring-blue-600/20(?!\s+dark:focus:ring-)', r'focus:ring-blue-600/20 dark:focus:ring-blue-400/20'),
        (r'focus:ring-green-600/20(?!\s+dark:focus:ring-)', r'focus:ring-green-600/20 dark:focus:ring-green-400/20'),
        (r'focus:ring-purple-600/20(?!\s+dark:focus:ring-)', r'focus:ring-purple-600/20 dark:focus:ring-purple-400/20'),
        (r'focus:ring-blue-500(?!\s+dark:focus:ring-)', r'focus:ring-blue-500 dark:focus:ring-blue-400'),
        (r'focus:ring-green-500(?!\s+dark:focus:ring-)', r'focus:ring-green-500 dark:focus:ring-green-400'),
        (r'focus:ring-purple-500(?!\s+dark:focus:ring-)', r'focus:ring-purple-500 dark:focus:ring-purple-400'),

        # Checkbox/Radio specific
        (r'text-blue-600\s+focus:ring-blue-500(?!\s+dark:)', r'text-blue-600 dark:text-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400'),
        (r'text-green-600\s+focus:ring-green-500(?!\s+dark:)', r'text-green-600 dark:text-green-400 focus:ring-green-500 dark:focus:ring-green-400'),
        (r'text-purple-600\s+focus:ring-purple-500(?!\s+dark:)', r'text-purple-600 dark:text-purple-400 focus:ring-purple-500 dark:focus:ring-purple-400'),
    ]

    for pattern, replacement in replacements:
        content = re.sub(pattern, replacement, content)

    return content, content != original

def process_file(file_path):
    """Process a single file"""
    print(f"Processing: {file_path}")

    try:
        # Read file
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()

        # Apply patterns
        new_content, changed = apply_dark_mode_patterns(content)

        if changed:
            # Backup original
            backup_path = f"{file_path}.backup"
            with open(backup_path, 'w', encoding='utf-8') as f:
                f.write(content)

            # Write new content
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(new_content)

            print(f"✓ Updated: {file_path}")
            return True
        else:
            print(f"  No changes needed: {file_path}")
            return False

    except Exception as e:
        print(f"✗ Error processing {file_path}: {e}")
        return False

def main():
    """Main function"""
    base_dir = Path(__file__).parent

    # List of files to process
    files = [
        # Form files
        "resources/views/incidents/create.blade.php",
        "resources/views/incidents/edit.blade.php",
        "resources/views/rcas/create.blade.php",
        "resources/views/rcas/edit.blade.php",
        "resources/views/contacts/create.blade.php",
        "resources/views/contacts/edit.blade.php",
        "resources/views/temporary-sites/create.blade.php",
        "resources/views/temporary-sites/edit.blade.php",
        "resources/views/users/create.blade.php",
        "resources/views/users/edit.blade.php",
        "resources/views/fbb-islands/create.blade.php",
        "resources/views/fbb-islands/edit.blade.php",
        "resources/views/sites/create.blade.php",
        "resources/views/sites/edit.blade.php",
        "resources/views/profile/edit.blade.php",
        # Index/List pages
        "resources/views/incidents/index.blade.php",
        "resources/views/rcas/index.blade.php",
        "resources/views/reports/index.blade.php",
        "resources/views/contacts/index.blade.php",
        "resources/views/smart-parser/index.blade.php",
        "resources/views/temporary-sites/index.blade.php",
        "resources/views/users/index.blade.php",
        "resources/views/fbb-islands/index.blade.php",
        "resources/views/sites/index.blade.php",
        "resources/views/logs/index.blade.php",
        # Show/Detail pages
        "resources/views/incidents/show.blade.php",
        "resources/views/rcas/show.blade.php",
        "resources/views/temporary-sites/show.blade.php",
        "resources/views/fbb-islands/show.blade.php",
        "resources/views/sites/show.blade.php",
        # Home page
        "resources/views/home.blade.php",
    ]

    print("=" * 60)
    print("Applying Dark Mode Patterns to Form Files")
    print("=" * 60)
    print()

    processed = 0
    updated = 0

    for file_rel in files:
        file_path = base_dir / file_rel
        if file_path.exists():
            processed += 1
            if process_file(file_path):
                updated += 1
        else:
            print(f"⚠ File not found: {file_path}")

    print()
    print("=" * 60)
    print(f"Completed: {processed} files processed, {updated} files updated")
    print("=" * 60)
    print()
    print("Backup files created with .backup extension")
    print("To restore: mv filename.backup filename")

if __name__ == "__main__":
    main()
