
document.addEventListener('DOMContentLoaded', () => {
    // Assign technician form handling
    const form = document.getElementById('assign-technician-form');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-HTTP-Method-Override': 'PUT',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) {
                    const error = await response.json();
                    alert(error.message || 'Assignment failed');
                    return;
                }
                window.location.reload();
            } catch (err) {
                console.error(err);
                alert('Something went wrong');
            }
        });
    }
});