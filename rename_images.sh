#!/bin/bash

# Specify the directory where your images are located
directory="public/storage/industrial/tread"

# Navigate to the directory
cd "$directory"

# Loop through all files with the pattern (102-o.jpg)
for file in *-1.jpg; do
    # Extract the prefix (e.g., 102-) and suffix (e.g., o.jpg)
    prefix="${file%-1.jpg}"
    suffix=".jpg"

    # Rename the file to the desired format (e.g., 102-1o.jpg)
    new_name="${prefix}-1o${suffix}"

    # Perform the rename operation
    mv "$file" "$new_name"
    
    # Optionally, print the renaming operation for each file
    echo "Renamed: $file -> $new_name"
done

echo "All files renamed."
