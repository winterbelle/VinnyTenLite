function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this message? This cannot be undone.")) {
        window.location.href = "admin_feedback.php?delete=" + id;
    }
}

function markRead(id) {
    window.location.href = buildUrlWithParam("mark_read", id);
}

function buildUrlWithParam(key, value) {
    const url = new URL(window.location.href);
    url.searchParams.set(key, value);
    return url.toString();
}

function openModal(id) {
    const overlay = document.getElementById('modal-overlay');
    const name   = document.getElementById('fb-name-' + id).textContent;
    const email  = document.getElementById('fb-email-' + id).textContent;
    const date   = document.getElementById('fb-date-' + id).textContent;
    const msg    = document.getElementById('fb-msg-' + id).textContent;

    document.getElementById('modal-name').textContent = name;
    document.getElementById('modal-email').textContent = email;
    document.getElementById('modal-date').textContent = date;
    document.getElementById('modal-message').textContent = msg;

    overlay.style.display = 'flex';
}

function closeModal() {
    document.getElementById('modal-overlay').style.display = 'none';
}
