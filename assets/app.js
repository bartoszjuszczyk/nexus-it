import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

document.addEventListener('DOMContentLoaded', function () {
    const avatarInput = document.getElementById('user_avatar');
    const avatarPreview = document.getElementById('avatar-preview');

    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function (event) {
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    avatarPreview.src = e.target.result;
                };

                reader.readAsDataURL(file);
            }
        });
    }

    const commentFileInput = document.getElementById('ticket_attachments');
    const commentFileListContainer = document.getElementById('file-list-container');

    if (commentFileInput && commentFileListContainer) {
        commentFileInput.addEventListener('change', function (event) {
            commentFileListContainer.innerHTML = '';

            if (commentFileInput.files.length > 0) {
                const list = document.createElement('ul');

                for (const file of commentFileInput.files) {
                    const listItem = document.createElement('li');
                    listItem.innerHTML = `<i class="fa-solid fa-file"></i> ${file.name}`;
                    list.appendChild(listItem);
                }

                commentFileListContainer.appendChild(list);
            }
        });
    }
});
