import os
import sys

def collect_code_files(root_dir, output_file="code_contents.txt", extensions=None):
    """
    Collect all code files and write their contents to a single text file.
    
    Args:
        root_dir: The root directory to start searching from
        output_file: Name of the output text file
        extensions: List of file extensions to include (default: common code extensions)
    """
    
    # Default extensions for code files
    if extensions is None:
        extensions = [
            # Web development
            '.html', '.htm', '.php', '.js', '.css', '.jsx', '.ts', '.tsx','.htaccess'
            # Python
            '.py', '.pyw', '.pyx',
            # Java
            '.java', '.jar', '.jsp',
            # C/C++
            '.c', '.cpp', '.cc', '.cxx', '.h', '.hpp',
            # C#
            '.cs',
            # Ruby
            '.rb', '.erb',
            # Go
            '.go',
            # Rust
            '.rs',
            # Shell
            '.sh', '.bash', '.zsh',
            # Configuration
            '.json', '.xml', '.yaml', '.yml', '.toml', '.ini', '.cfg', '.conf',
            # Database
            '.sql',
            # Markdown and documentation
            '.md', '.txt', '.rst',
            # Other
            '.swift', '.kt', '.scala', '.fs', '.vb', '.pl', '.pm', '.r', '.lua'
        ]
    
    # Convert extensions to lowercase for case-insensitive matching
    extensions = [ext.lower() for ext in extensions]
    
    try:
        # Get absolute path of root directory
        root_dir = os.path.abspath(root_dir)
        
        # Check if root directory exists
        if not os.path.exists(root_dir):
            print(f"Error: Directory '{root_dir}' does not exist.")
            return
        
        print(f"Scanning directory: {root_dir}")
        print(f"Including files with extensions: {', '.join(extensions)}")
        
        # Counters
        files_processed = 0
        total_size = 0
        
        with open(output_file, 'w', encoding='utf-8') as outfile:
            # Write header
            outfile.write(f"CODE FILES COLLECTION\n")
            outfile.write(f"Root Directory: {root_dir}\n")
            outfile.write(f"Generated on: {os.path.getctime(output_file)}\n")
            outfile.write("=" * 80 + "\n\n")
            
            # Walk through all directories
            for dirpath, dirnames, filenames in os.walk(root_dir):
                # Skip hidden directories (starting with .)
                dirnames[:] = [d for d in dirnames if not d.startswith('.')]
                
                for filename in filenames:
                    # Skip hidden files
                    if filename.startswith('.'):
                        continue
                    
                    # Get file extension
                    _, file_ext = os.path.splitext(filename)
                    
                    # Check if file has a valid extension
                    if file_ext.lower() in extensions:
                        file_path = os.path.join(dirpath, filename)
                        rel_path = os.path.relpath(file_path, root_dir)
                        
                        try:
                            # Read file content
                            with open(file_path, 'r', encoding='utf-8') as infile:
                                content = infile.read()
                            
                            # Write separator and file info
                            outfile.write(f"\n{'=' * 80}\n")
                            outfile.write(f"FILE: {rel_path}\n")
                            outfile.write(f"{'=' * 80}\n\n")
                            
                            # Write file content
                            outfile.write(content)
                            
                            # Add newline if file doesn't end with one
                            if content and not content.endswith('\n'):
                                outfile.write('\n')
                            
                            # Update counters
                            files_processed += 1
                            total_size += len(content.encode('utf-8'))
                            
                            print(f"✓ Added: {rel_path}")
                            
                        except UnicodeDecodeError:
                            # Skip binary files or files with different encoding
                            print(f"✗ Skipped (binary/encoding): {rel_path}")
                        except PermissionError:
                            print(f"✗ Skipped (permission denied): {rel_path}")
                        except Exception as e:
                            print(f"✗ Error reading {rel_path}: {str(e)}")
        
        # Write footer with summary
        with open(output_file, 'a', encoding='utf-8') as outfile:
            outfile.write(f"\n{'=' * 80}\n")
            outfile.write(f"SUMMARY\n")
            outfile.write(f"Total files processed: {files_processed}\n")
            outfile.write(f"Total characters: {total_size}\n")
            outfile.write(f"Output file: {os.path.abspath(output_file)}\n")
        
        print(f"\n{'=' * 50}")
        print(f"SUCCESS!")
        print(f"Output saved to: {os.path.abspath(output_file)}")
        print(f"Total files processed: {files_processed}")
        print(f"Total size: {total_size} characters")
        
    except Exception as e:
        print(f"Error: {str(e)}")

def main():
    """Main function to run the script."""
    
    print("Code File Collector")
    print("=" * 50)
    
    # Get directory to scan
    if len(sys.argv) > 1:
        directory = sys.argv[1]
    else:
        directory = input("Enter the directory path to scan (or '.' for current directory): ").strip()
        if not directory:
            directory = "."
    
    # Get output filename
    if len(sys.argv) > 2:
        output_filename = sys.argv[2]
    else:
        output_filename = input("Enter output filename [default: code_contents.txt]: ").strip()
        if not output_filename:
            output_filename = "code_contents.txt"
    
    # Get custom extensions if wanted
    custom_extensions = input("Enter custom file extensions separated by commas (or press Enter for default): ").strip()
    
    if custom_extensions:
        extensions = [ext.strip() for ext in custom_extensions.split(',')]
        extensions = [ext if ext.startswith('.') else f'.{ext}' for ext in extensions]
        print(f"Using custom extensions: {extensions}")
        collect_code_files(directory, output_filename, extensions)
    else:
        collect_code_files(directory, output_filename)

if __name__ == "__main__":
    main()