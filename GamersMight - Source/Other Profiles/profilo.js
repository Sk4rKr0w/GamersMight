function toggleSidebar() {
    var sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('active');
}

function validateSearchInput() {
    var searchInput = document.querySelector('.search-bar');
    var submitBtn = document.getElementById('submitBtn');
    if (searchInput.value.trim() !== '') {
        submitBtn.disabled = false;
    } else {
        submitBtn.disabled = true;
    }
}  