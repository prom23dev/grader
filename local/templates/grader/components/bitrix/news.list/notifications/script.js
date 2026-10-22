function redirectToCreateProject() {
    window.location.href = '/projects/create/'
}

document.addEventListener('click', function (e) {
    const removeButton = e.target.closest('.notifications__list-item__remove');
    if (removeButton) {
        e.preventDefault();
        const item = removeButton.closest('.notifications__list-item');
        const id = removeButton.dataset.notificationId;
        const sessid = removeButton.dataset.sessid;
        removeButton.disabled = true;
        fetch('/local/templates/grader/assets/ajax/deleteNotification.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + encodeURIComponent(id) + '&sessid=' + encodeURIComponent(sessid),
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data.success) {
                    item.classList.add('is-removing');
                    setTimeout(function () {
                        item.remove();
                        const list = document.getElementById('notificationsList');
                        if (list && !list.children.length) {
                            window.location.reload();
                        }
                    }, 200);
                } else {
                    removeButton.disabled = false;
                }
            })
            .catch(function () {
                removeButton.disabled = false;
            });
        return;
    }

    const clearAllButton = e.target.closest('#clearAllNotifications');
    if (clearAllButton) {
        e.preventDefault();
        if (!confirm('Удалить все уведомления?')) {
            return;
        }
        clearAllButton.disabled = true;
        const originalText = clearAllButton.textContent;
        const sessid = clearAllButton.dataset.sessid;

        const clearBatch = function () {
            fetch('/local/templates/grader/assets/ajax/clearNotifications.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'sessid=' + encodeURIComponent(sessid),
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (!data.success) {
                        clearAllButton.disabled = false;
                        clearAllButton.textContent = originalText;
                        return;
                    }
                    if (data.remaining > 0) {
                        clearAllButton.textContent = 'Удаление... осталось ' + data.remaining;
                        clearBatch();
                    } else {
                        window.location.reload();
                    }
                })
                .catch(function () {
                    clearAllButton.disabled = false;
                    clearAllButton.textContent = originalText;
                });
        };

        clearBatch();
    }
});
