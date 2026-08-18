// Mirrors the native <input accept> matching rules: a comma-separated list of
// file extensions (".pdf"), full mime types ("application/pdf"), or mime
// group wildcards ("image/*").
export function matchesAcceptFile(file, acceptFile) {
    if (!acceptFile) {
        return true;
    }
    const patterns = acceptFile.split(',').map((p) => p.trim().toLowerCase()).filter(Boolean);
    if (patterns.length === 0) {
        return true;
    }
    const fileName = (file.name || '').toLowerCase();
    const mimeType = (file.type || '').toLowerCase();

    return patterns.some((pattern) => {
        if (pattern.charAt(0) === '.') {
            return fileName.endsWith(pattern);
        }
        if (pattern.endsWith('/*')) {
            return mimeType.startsWith(pattern.slice(0, -1));
        }

        return mimeType === pattern;
    });
}
