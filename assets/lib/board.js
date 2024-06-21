document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-delete').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            const href = button.dataset.url;
            if (!href) {
                alert('No URL provided');
            }

            fetch(href, {
                method: 'DELETE',
            }).then((response) => {
                if (response.ok) {
                    button.closest('.object').remove();
                } else {
                    alert('Failed to delete');
                }
            });
        })
    })
})
