#!/bin/bash

# Script to apply dark mode patterns to all form files
# Based on DARK_MODE_FORM_PATTERNS.md

set -e

echo "Applying dark mode patterns to form files..."

# Function to apply dark mode patterns to a file
apply_dark_mode() {
    local file=$1
    echo "Processing: $file"

    # Backup original file
    cp "$file" "$file.backup"

    # Apply replacements using sed

    # 1. Update border-gray-300 (not followed by dark:)
    sed -i '' 's/border-gray-300\([^"]*\)\([^d]\|$\)/border-gray-300 dark:border-gray-600\1\2/g' "$file"

    # 2. Update bg-white for inputs (not followed by dark:)
    sed -i '' 's/bg-white\([^"]*\)\([^d]\|$\)/bg-white dark:bg-gray-800\1\2/g' "$file"

    # 3. Add text-gray-900 dark:text-gray-100 to inputs/selects/textareas
    # This requires more complex pattern matching, skip for now

    # 4. Update placeholder colors
    sed -i '' 's/placeholder-gray-400\([^"]*\)\([^d]\|$\)/placeholder-gray-400 dark:placeholder-gray-500\1\2/g' "$file"

    # 5. Update focus border colors
    sed -i '' 's/focus:border-blue-600\([^"]*\)\([^d]\|$\)/focus:border-blue-600 dark:focus:border-blue-400\1\2/g' "$file"
    sed -i '' 's/focus:border-green-600\([^"]*\)\([^d]\|$\)/focus:border-green-600 dark:focus:border-green-400\1\2/g' "$file"
    sed -i '' 's/focus:border-purple-600\([^"]*\)\([^d]\|$\)/focus:border-purple-600 dark:focus:border-purple-400\1\2/g' "$file"

    # 6. Update focus ring colors
    sed -i '' 's/focus:ring-blue-600\/20\([^"]*\)\([^d]\|$\)/focus:ring-blue-600\/20 dark:focus:ring-blue-400\/20\1\2/g' "$file"
    sed -i '' 's/focus:ring-green-600\/20\([^"]*\)\([^d]\|$\)/focus:ring-green-600\/20 dark:focus:ring-green-400\/20\1\2/g' "$file"
    sed -i '' 's/focus:ring-purple-600\/20\([^"]*\)\([^d]\|$\)/focus:ring-purple-600\/20 dark:focus:ring-purple-400\/20\1\2/g' "$file"

    # 7. Update validation error borders
    sed -i '' 's/border-red-300\([^"]*\)\([^d]\|$\)/border-red-300 dark:border-red-700\1\2/g' "$file"

    # 8. Update text colors for labels and help text
    sed -i '' 's/text-gray-700\([^"]*\)\([^d]\|$\)/text-gray-700 dark:text-gray-300\1\2/g' "$file"
    sed -i '' 's/text-gray-500\([^"]*\)\([^d]\|$\)/text-gray-500 dark:text-gray-400\1\2/g' "$file"
    sed -i '' 's/text-gray-600\([^"]*\)\([^d]\|$\)/text-gray-600 dark:text-gray-400\1\2/g' "$file"

    # 9. Update error message colors
    sed -i '' 's/text-red-600\([^"]*\)\([^d]\|$\)/text-red-600 dark:text-red-400\1\2/g' "$file"

    # 10. Update headings
    sed -i '' 's/text-gray-900\([^"]*\)\([^d]\|$\)/text-gray-900 dark:text-gray-100\1\2/g' "$file"

    # 11. Update container backgrounds
    sed -i '' 's/bg-white\/80\([^"]*\)\([^d]\|$\)/bg-white\/80 dark:bg-gray-800\/80\1\2/g' "$file"

    # 12. Update border colors for containers
    sed -i '' 's/border-gray-200\([^"]*\)\([^d]\|$\)/border-gray-200 dark:border-gray-700\1\2/g' "$file"
    sed -i '' 's/border-gray-100\([^"]*\)\([^d]\|$\)/border-gray-100 dark:border-gray-700\1\2/g' "$file"

    # 13. Update specific colored borders
    sed -i '' 's/border-green-200\([^"]*\)\([^d]\|$\)/border-green-200 dark:border-green-700\1\2/g' "$file"
    sed -i '' 's/border-green-300\([^"]*\)\([^d]\|$\)/border-green-300 dark:border-green-700\1\2/g' "$file"
    sed -i '' 's/border-purple-200\([^"]*\)\([^d]\|$\)/border-purple-200 dark:border-purple-700\1\2/g' "$file"
    sed -i '' 's/border-purple-300\([^"]*\)\([^d]\|$\)/border-purple-300 dark:border-purple-700\1\2/g' "$file"
    sed -i '' 's/border-blue-200\([^"]*\)\([^d]\|$\)/border-blue-200 dark:border-blue-700\1\2/g' "$file"

    # 14. Update checkbox/radio text colors
    sed -i '' 's/text-blue-600 focus:ring-blue-500/text-blue-600 dark:text-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400/g' "$file"
    sed -i '' 's/text-green-600 focus:ring-green-500/text-green-600 dark:text-green-400 focus:ring-green-500 dark:focus:ring-green-400/g' "$file"
    sed -i '' 's/text-purple-600 focus:ring-purple-500/text-purple-600 dark:text-purple-400 focus:ring-purple-500 dark:focus:ring-purple-400/g' "$file"

    # 15. Update info/help text colors
    sed -i '' 's/text-green-700\([^"]*\)\([^d]\|$\)/text-green-700 dark:text-green-400\1\2/g' "$file"
    sed -i '' 's/text-purple-700\([^"]*\)\([^d]\|$\)/text-purple-700 dark:text-purple-400\1\2/g' "$file"
    sed -i '' 's/text-blue-700\([^"]*\)\([^d]\|$\)/text-blue-700 dark:text-blue-400\1\2/g' "$file"

    echo "✓ Completed: $file"
}

# List of form files to update
files=(
    "resources/views/incidents/create.blade.php"
    "resources/views/incidents/edit.blade.php"
    "resources/views/rcas/create.blade.php"
    "resources/views/rcas/edit.blade.php"
    "resources/views/contacts/create.blade.php"
    "resources/views/contacts/edit.blade.php"
    "resources/views/temporary-sites/create.blade.php"
    "resources/views/temporary-sites/edit.blade.php"
    "resources/views/users/create.blade.php"
    "resources/views/users/edit.blade.php"
    "resources/views/fbb-islands/create.blade.php"
    "resources/views/fbb-islands/edit.blade.php"
    "resources/views/sites/create.blade.php"
    "resources/views/sites/edit.blade.php"
    "resources/views/profile/edit.blade.php"
)

# Apply to all files
for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        apply_dark_mode "$file"
    else
        echo "⚠ File not found: $file"
    fi
done

echo ""
echo "====================================="
echo "Dark mode patterns applied successfully!"
echo "====================================="
echo ""
echo "Backup files created with .backup extension"
echo "To restore a file: mv filename.backup filename"
echo ""
echo "Next steps:"
echo "1. Test the forms in dark mode"
echo "2. Check for any missed patterns"
echo "3. If satisfied, remove backup files: rm resources/views/**/*.backup"
