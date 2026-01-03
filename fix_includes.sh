#!/bin/bash

cd /Applications/XAMPP/xamppfiles/htdocs/OOP_PROJECT/hospital-management-system/views

echo "Fixing include paths in PHP files..."

# Create backup directory
mkdir -p ../backups/views

# Process each PHP file
for file in $(find . -name "*.php" -type f); do
    echo "Processing: $file"
    
    # Create backup
    cp "$file" "../backups/views/$(basename $file).backup"
    
    # Fix header includes
    sed -i '' "s|include 'views/layouts/header.php';|include __DIR__ . '/../layouts/header.php';|g" "$file"
    sed -i '' "s|include \"views/layouts/header.php\";|include __DIR__ . '/../layouts/header.php';|g" "$file"
    sed -i '' "s|include 'layouts/header.php';|include __DIR__ . '/../layouts/header.php';|g" "$file"
    sed -i '' "s|include \"layouts/header.php\";|include __DIR__ . '/../layouts/header.php';|g" "$file"
    
    # Fix footer includes
    sed -i '' "s|include 'views/layouts/footer.php';|include __DIR__ . '/../layouts/footer.php';|g" "$file"
    sed -i '' "s|include \"views/layouts/footer.php\";|include __DIR__ . '/../layouts/footer.php';|g" "$file"
    sed -i '' "s|include 'layouts/footer.php';|include __DIR__ . '/../layouts/footer.php';|g" "$file"
    sed -i '' "s|include \"layouts/footer.php\";|include __DIR__ . '/../layouts/footer.php';|g" "$file"
    
    # Also fix any variations
    sed -i '' "s|^include_once 'views/layouts|include_once __DIR__ . '/../layouts|g" "$file"
    sed -i '' "s|^require 'views/layouts|require __DIR__ . '/../layouts|g" "$file"
    sed -i '' "s|^require_once 'views/layouts|require_once __DIR__ . '/../layouts|g" "$file"
done

echo "Done! Backups saved to: ../backups/views/"