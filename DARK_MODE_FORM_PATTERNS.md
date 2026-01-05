# Dark Mode Form Control Patterns

This document defines standardized CSS classes for all form controls with dark mode support.
Use these patterns consistently across all forms in the application.

## 1. Text Inputs (input type="text", "email", "password", "number", etc.)

### Standard Text Input
```html
<input type="text"
    class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 shadow-sm focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 transition-all duration-300">
```

### With Validation Error
```html
<input type="text"
    class="w-full rounded-xl border @error('field_name') border-red-300 dark:border-red-700 @else border-gray-300 dark:border-gray-600 @enderror px-4 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 shadow-sm focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 transition-all duration-300">
```

### Disabled State
```html
<input type="text" disabled
    class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 bg-gray-100 dark:bg-gray-900 text-gray-500 dark:text-gray-500 cursor-not-allowed opacity-60">
```

## 2. Textarea

### Standard Textarea
```html
<textarea rows="4"
    class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 shadow-sm focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 transition-all duration-300"></textarea>
```

### With Validation Error
```html
<textarea rows="4"
    class="w-full rounded-xl border @error('field_name') border-red-300 dark:border-red-700 @else border-gray-300 dark:border-gray-600 @enderror px-4 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 shadow-sm focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 transition-all duration-300"></textarea>
```

## 3. Select Dropdown

### Standard Select
```html
<select
    class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 transition-all duration-300">
    <option value="">Select...</option>
</select>
```

### With Validation Error
```html
<select
    class="w-full rounded-xl border @error('field_name') border-red-300 dark:border-red-700 @else border-gray-300 dark:border-gray-600 @enderror px-4 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 transition-all duration-300">
    <option value="">Select...</option>
</select>
```

## 4. Checkbox

### Standard Checkbox
```html
<input type="checkbox"
    class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 transition-colors">
```

### Checkbox Label
```html
<label class="flex items-center space-x-3 cursor-pointer">
    <input type="checkbox" class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 transition-colors">
    <span class="text-sm text-gray-700 dark:text-gray-300">Label text</span>
</label>
```

## 5. Radio Button

### Standard Radio
```html
<input type="radio"
    class="w-4 h-4 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 transition-colors">
```

### Radio with Label
```html
<label class="flex items-center space-x-3 cursor-pointer">
    <input type="radio" name="option" class="w-4 h-4 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 transition-colors">
    <span class="text-sm text-gray-700 dark:text-gray-300">Option text</span>
</label>
```

## 6. File Input

### Standard File Input
```html
<input type="file"
    class="w-full rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 dark:file:bg-blue-900/30 file:text-blue-700 dark:file:text-blue-400 file:font-medium hover:file:bg-blue-100 dark:hover:file:bg-blue-900/50 focus:outline-none focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 transition-all duration-300">
```

## 7. Labels

### Standard Label
```html
<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
    Field Label
</label>
```

### Required Field Label
```html
<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
    Field Label <span class="text-red-600 dark:text-red-400">*</span>
</label>
```

## 8. Help Text

### Standard Help Text
```html
<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
    Helper text explaining the field
</p>
```

### Info Help Text (with icon)
```html
<p class="text-sm text-blue-600 dark:text-blue-400 mb-2">
    Important information
</p>
```

## 9. Validation Error Messages

### Error Message
```html
@error('field_name')
    <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
@enderror
```

## 10. Search Input (with icon)

### Search Input
```html
<div class="relative">
    <input type="text" placeholder="Search..."
        class="w-full rounded-xl border border-gray-300 dark:border-gray-600 pl-10 pr-4 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 shadow-sm focus:border-blue-600 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 transition-all duration-300">
    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
    </svg>
</div>
```

## 11. Form Sections/Groups

### Form Section Container
```html
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
    <!-- Form fields here -->
</div>
```

### Form Section Header
```html
<h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
    Section Title
</h3>
```

### Form Section Divider
```html
<hr class="border-gray-200 dark:border-gray-700 my-6">
```

## 12. Buttons (for reference)

### Primary Button
```html
<button type="submit"
    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-medium rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-600/20 dark:focus:ring-blue-400/20 transition-all duration-300">
    Submit
</button>
```

### Secondary Button
```html
<button type="button"
    class="px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400/20 transition-all duration-300">
    Cancel
</button>
```

### Danger Button
```html
<button type="button"
    class="px-6 py-3 bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white font-medium rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-red-600/20 dark:focus:ring-red-400/20 transition-all duration-300">
    Delete
</button>
```

## Pattern Summary for Quick Reference

### Quick Pattern Replacements

#### Find: `border-gray-300`
Replace with: `border-gray-300 dark:border-gray-600`

#### Find: `bg-white` (for inputs)
Replace with: `bg-white dark:bg-gray-800`

#### Find: `text-gray-900` (for inputs)
Add if missing: `text-gray-900 dark:text-gray-100`

#### Find: `placeholder-gray-400`
Replace with: `placeholder-gray-400 dark:placeholder-gray-500`

#### Find: `focus:border-blue-600`
Replace with: `focus:border-blue-600 dark:focus:border-blue-400`

#### Find: `focus:ring-blue-600/20`
Replace with: `focus:ring-blue-600/20 dark:focus:ring-blue-400/20`

#### Find: `border-red-300` (validation errors)
Replace with: `border-red-300 dark:border-red-700`

#### Find: `text-red-600` (error messages)
Replace with: `text-red-600 dark:text-red-400`

## Special Cases

### Date/Time Inputs
Same as standard text input pattern.

### Number Inputs with Step Controls
Same as standard text input pattern.

### Color Picker
```html
<input type="color"
    class="h-10 w-20 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 cursor-pointer">
```

### Range Slider
```html
<input type="range"
    class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer accent-blue-600 dark:accent-blue-400">
```

---

## Implementation Notes

1. **Consistency**: Always use these exact patterns across all forms
2. **Validation**: Always include error state variants with `@error` directive
3. **Labels**: Every input should have an associated label (visible or sr-only)
4. **Help Text**: Use gray-500/gray-400 for non-critical help text
5. **Required Fields**: Mark with red asterisk using `text-red-600 dark:text-red-400`
6. **Transitions**: Include `transition-all duration-300` for smooth theme switching
7. **Focus States**: Always include focus ring for accessibility

## Files to Update

### Create Forms
- [ ] incidents/create.blade.php
- [ ] rcas/create.blade.php
- [ ] contacts/create.blade.php
- [ ] temporary-sites/create.blade.php
- [ ] users/create.blade.php
- [ ] fbb-islands/create.blade.php
- [ ] sites/create.blade.php

### Edit Forms
- [ ] incidents/edit.blade.php
- [ ] rcas/edit.blade.php
- [ ] contacts/edit.blade.php
- [ ] temporary-sites/edit.blade.php
- [ ] users/edit.blade.php
- [ ] fbb-islands/edit.blade.php
- [ ] sites/edit.blade.php
- [ ] profile/edit.blade.php

### Other Forms
- [ ] smart-parser (if has forms)
- [ ] reports (if has forms)
- [ ] Any modal forms or inline edit forms
