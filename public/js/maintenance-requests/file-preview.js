function openPreview(fileUrl, fileName, fileType) {
    const modal = document.getElementById('previewModal');
    const title = document.getElementById('previewTitle');
    const content = document.getElementById('previewContent');

    title.textContent = fileName;

    if (fileType === 'image') {
        content.innerHTML = `<img src="${fileUrl}" alt="${fileName}" class="max-h-full max-w-full object-contain" />`;
    } else if (fileType === 'pdf') {
        content.innerHTML = `<iframe src="${fileUrl}" class="w-full h-full" frameborder="0"></iframe>`;
    } else {
        content.innerHTML = `<p class="text-sm text-gray-700 dark:text-gray-300">Preview not available for this file type.</p>`;
    }

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePreview() {
    const modal = document.getElementById('previewModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('previewContent').innerHTML = ''; // clear content
}




// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('previewModal').classList.contains('hidden')) {
        closePreview();
    }
});