document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', function () {
            const blogId = this.dataset.postId;

            fetch('/likes/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ blog_id: blogId }),
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur du serveur.');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const likeCount = document.getElementById(`like-count-${blogId}`);
                    const count = data.likes_count;
                    likeCount.textContent = `${count} ${count === 1 ? 'Like' : 'Likes'}`;

                    const icon = this.querySelector('i');
                    icon.classList.toggle('bi-heart');
                    icon.classList.toggle('bi-heart-fill');
                } else {
                    console.error(data.message || 'Une erreur est survenue.');
                }
            })
            .catch(error => {
                console.error('Erreur lors de la mise à jour du like :', error);
            });
        });
    });
});
