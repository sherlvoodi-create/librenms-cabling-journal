// Скрытие пустых юнитов
function toggleEmpty() {
    const hide = document.getElementById('hideEmptyUnits').checked;
    document.querySelectorAll('.empty-u').forEach(u => {
        u.style.display = hide ? 'none' : 'flex';
    });
}

// Кастомный тултип
const tooltip = document.getElementById('custom-tooltip');
document.addEventListener('mouseover', e => {
    const tip = e.target.getAttribute('data-tip');
    if (tip) {
        tooltip.innerHTML = tip.replace('|', '<br><small>') + '</small>';
        tooltip.style.display = 'block';
    }
});
document.addEventListener('mousemove', e => {
    if (tooltip.style.display === 'block') {
        tooltip.style.left = (e.clientX + 15) + 'px';
        tooltip.style.top = (e.clientY + 15) + 'px';
    }
});
document.addEventListener('mouseout', () => tooltip.style.display = 'none');

// Модалка добавления
function openAddModal(u) {
    document.getElementById('modal-u-num').innerText = u;
    document.getElementById('modal-u-input').value = u;
    $('#addPanelModal').modal('show');
}
